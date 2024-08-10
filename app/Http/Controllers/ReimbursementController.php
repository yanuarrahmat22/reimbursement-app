<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Reimbursement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ReimbursementController extends Controller
{
  protected $get_menu;

  public function __construct()
  {
    $this->middleware('auth');

    $this->get_menu = get_menu_id('reimbursement');
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    if (request()->ajax()) {
      if (Auth::user()->role->code == 'ST') {
        $datas = Reimbursement::orderBy('created_at', 'desc')->where('user_created', Auth::user()->id);
      } else {
        $datas = Reimbursement::orderBy('created_at', 'desc');
      }

      if (!empty($request->get('startdatetime_created')) && !empty($request->get('enddatetime_created'))) {
        $datas = $datas->where(function ($query) use ($request) {
          $query->whereBetween('date_created', [$request->get('startdatetime_created'), $request->get('enddatetime_created')]);
        });
      }

      if (!empty($request->get('status'))) {
        $datas = $datas->where(function ($query) use ($request) {
          $query->where('status', $request->status);
        });
      }

      $datas = $datas->get();

      return DataTables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['name_submission']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('action', function ($data) {
          //get module akses
          $id_menu = get_menu_id('reimbursement');

          //edit
          $btn_detail = '';
          if (isAccess('read', $id_menu, auth()->user()->role_id)) {
            $btn_detail = '<a class="dropdown-item" href="' . route('reimbursement.show', $data->id) . '"><i class="fas fa-eye me-1"></i> Detail</a>';
          }

          //edit
          $btn_edit = '';

          if (isAccess('update', $id_menu, auth()->user()->role_id)) {
            if ($data->status == 'waiting') {
              if (Auth::user()->id == $data->user_created) {
                $btn_edit = '<a class="dropdown-item" href="' . route('reimbursement.edit', $data->id) . '"><i class="fas fa-pencil-alt me-1"></i> Edit</a>';
              }
            }
          }

          // Approval direktur
          $btn_checking_application = '';

          if (isAccess('approval', $id_menu, auth()->user()->role_id)) {
            if ($data->status == 'waiting') {
              $btn_checking_application = '<a class="dropdown-item" href="' . route('reimbursement.checking', $data->id) . '"><i class="fas fa-user-check me-1"></i> Cek Pengajuan</a>';
            }
          }

          // Konfirmasi pembayaran finance
          $btn_payment_confirmation = '';

          if (isAccess('payment-confirmation', $id_menu, auth()->user()->role_id)) {
            if ($data->status == 'approved') {
              $btn_payment_confirmation = '<a class="dropdown-item btn-payment-confirmation text-info" href="javascript:void(0)" data-id="' . $data->id . '" data-nama="' . $data->name . '"><i class="fas fa-money-check-alt me-1"></i> Konfirmasi Pembayaran</a>';
            }
          }

          //delete
          $btn_hapus = '';

          if (isAccess('delete', $id_menu, auth()->user()->role_id)) {
            if ($data->status == 'waiting') {
              if (Auth::user()->id == $data->user_created) {
                $btn_hapus = '<hr class="dropdown-divider"><a class="dropdown-item btn-hapus text-danger" href="javascript:void(0)" data-id="' . $data->id . '" data-nama="' . $data->name . '"><i class="fas fa-trash-alt me-1"></i> Hapus</a>';
              }
            }
          }

          return '
              <div class="d-inline-block">
                <a href="javascript:;" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </a>

                <div class="dropdown-menu dropdown-menu-end m-0 menu-action-datatable" style="">
                  ' . $btn_detail . '
                  ' . $btn_edit . '
                  ' . $btn_checking_application . '
                  ' . $btn_payment_confirmation . '
                  ' . $btn_hapus . '
                </div>
              </div>
          ';
        })
        ->addColumn('user_created', function ($data) {
          return $data->usercreated->name . ' - <strong>' . $data->usercreated->nip . '</strong>';
        })
        ->addColumn('date_created', function ($data) {
          return $data->date_created != null ? Carbon::createFromFormat('Y-m-d', $data->date_created)->isoFormat('D MMMM YYYY') : '-';
        })
        ->addColumn('name_submission', function ($data) {
          return $data->name;
        })
        ->addColumn('description', function ($data) {
          $description = $data->description;

          if ($data->description != null || $data->description != '') {
            $description = Str::limit($data->description, 50, '....');
          }

          return $description;
        })
        ->addColumn('status_detail', function ($data) {
          $html = '';

          if ($data->status == 'done') {
            $html = '<span class="badge rounded-pill bg-info">Selesai Dibayar</span>';
          } elseif ($data->status == 'approved') {
            $html = '<div class="d-flex justify-content-start align-items-center user-name">
            <div class="d-flex flex-column">
            <span class="emp_name text-truncate">Disetujui Oleh: <strong>' . $data->userapproved->role->name . '</strong></span>
            <small class="emp_post text-truncate text-muted">Tanggal Disetujui: ' . Carbon::createFromFormat('Y-m-d', $data->date_approved)->isoFormat('D MMMM YYYY') . '</small>
            </div>
            </div>';
          } elseif ($data->status == 'rejected') {
            $html = '<div class="d-flex justify-content-start align-items-center user-name">
            <div class="d-flex flex-column">
            <span class="emp_name text-truncate">Ditolak Oleh: <strong>' . $data->userapproved->role->name . '</strong></span>
            </div>
            </div>';
          } else {
            $html = '<span class="badge rounded-pill bg-warning">Sedang Diproses</span>';
          }

          return $html;
        })
        ->addColumn('status_color', function ($data) {
          $status_color = '';

          if ($data->status == 'waiting') {
            $status_color = 'waiting';
          } elseif ($data->status == 'rejected') {
            $status_color = 'rejected';
          } elseif ($data->status == 'done') {
            $status_color = 'done';
          } else {
            $status_color = 'approved';
          }

          return $status_color;
        })
        ->rawColumns([
          'action',
          'name_submission',
          'user_created',
          'date_created',
          'description',
          'status_detail',
          'status_color',
        ])
        ->addIndexColumn() //increment
        ->make(true);
    };

    $get_menu = $this->get_menu;

    if (isAccess('list', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.reimbursement.index', compact('get_menu'));
    } else {
      abort(419);
    }
  }



  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    if (isAccess('create', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.reimbursement.create');
    } else {
      abort(419);
    }
  }

  public function rules_store($request)
  {
    $rule = [
      'date_created' => 'required',
      'name' => 'required|string|max:200',
      'description' => 'required|string',
      'file' => 'max:5000|mimes:png,jpeg,gif,pdf|required',
    ];

    $pesan = [
      'date_created.required' => 'Tanggal pengajuan wajib diisi!',
      'name.required' => 'Nama pengajuan wajib diisi!',
      'name.max' => 'Nama pengajuan wajib diisi dengan maksimal 200 karakter!',
      'description.required' => 'Deskripsi pengajuan wajib diisi!',
      'file.required' => 'File pendukung tidak boleh kosong!',
      'file.max' => 'File pendukung tidak boleh lebih dari 5MB!',
      'file.mimes' => 'File pendukung format hanya .png, .jpeg, .gif, atau .pdf!',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validator = $this->rules_store($request->all());

    // dd($request->all());

    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'pesan' => $validator->errors(),
      ]);
    } else {
      DB::beginTransaction();

      try {
        $post = new Reimbursement();
        $post->user_created = Auth::user()->id;
        $post->date_created = $request->date_created;
        $post->name = $request->name;
        $post->description = $request->description;
        $post->status = 'waiting';

        if ($request->hasFile('file')) {
          $post->file = $request->file('file')->store('file-reimbursement', 'public');
        }

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json([
            'status' => true,
            'pesan' => "Data pengajuan berhasil dibuat!",
          ], 200);
        } else {
          return response()->json([
            'status' => false,
            'pesan' => "Data pengajuan tidak dapat dibuat!",
          ], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json([
          'status' => false,
          'pesan' => $e->getMessage(),
        ], 200);
      }
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $item = Reimbursement::findOrFail($id);

    if (isAccess('read', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.reimbursement.show', compact(
        'item',
      ));
    } else {
      abort(419, 'Anda tidak mengakses halaman ini.');
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    $item = Reimbursement::findOrFail($id);

    if (isAccess('read', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.reimbursement.edit', compact(
        'item',
      ));
    } else {
      abort(419, 'Anda tidak mengakses halaman ini.');
    }
  }

  // validation untuk form update
  public function rules_update($request)
  {
    $rule = [
      'date_created' => 'required',
      'name' => 'required|string|max:200',
      'description' => 'required|string',
      'file' => 'max:5000|mimes:png,jpeg,gif,pdf|sometimes|nullable',
    ];

    $pesan = [
      'date_created.required' => 'Tanggal pengajuan wajib diisi!',
      'name.required' => 'Nama pengajuan wajib diisi!',
      'name.max' => 'Nama pengajuan wajib diisi dengan maksimal 200 karakter!',
      'description.required' => 'Deskripsi pengajuan wajib diisi!',
      'file.max' => 'File pendukung tidak boleh lebih dari 5MB!',
      'file.mimes' => 'File pendukung format hanya .png, .jpeg, .gif, atau .pdf!',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $validator = $this->rules_update($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $post = Reimbursement::find($id);
        $post->date_created = $request->date_created;
        $post->name = $request->name;
        $post->description = $request->description;


        if ($request->hasFile('file')) {
          if ($post->file != null) {
            if (Storage::disk('public')->exists($post->file)) {
              Storage::disk('public')->delete($post->file);
            }
          }

          $post->file = $request->file('file')->store('file-reimbursement', 'public');
        }

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json([
            'status' => true,
            'pesan' => "Data pengajuan berhasil diubah!"
          ], 200);
        } else {
          return response()->json([
            'status' => false,
            'pesan' => "Data pengajuan tidak berhasil diubah!"
          ], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $query_reimbursement = Reimbursement::where('id', $id);

    $item_reimbursement = $query_reimbursement->first();

    if ($item_reimbursement->file != null) {
      if (Storage::disk('public')->exists($item_reimbursement->file)) {
        Storage::disk('public')->delete($item_reimbursement->file);
      }
    }

    $hapus = $query_reimbursement->delete();

    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data pengajuan berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data pengajuan tidak berhasil dihapus!"], 200);
    }
  }

  /**
   * Cheking.
   */
  public function checkingApplication(string $id)
  {
    $item = Reimbursement::findOrFail($id);

    if (isAccess('approval', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.reimbursement.approval', compact(
        'item',
      ));
    } else {
      abort(419, 'Anda tidak mengakses halaman ini.');
    }
  }

  /**
   * Store Cheking.
   */
  public function storeCheckingApplication(Request $request, string $id)
  {
    DB::beginTransaction();

    try {
      $post = Reimbursement::find($id);
      $post->user_approved = Auth::user()->id;
      $post->date_approved = date('Y-m-d');
      $post->status = $request->status;

      $simpan = $post->save();

      DB::commit();

      if ($simpan == true) {
        return response()->json([
          'status' => true,
          'pesan' => "Data pengajuan berhasil diubah!"
        ], 200);
      } else {
        return response()->json([
          'status' => false,
          'pesan' => "Data pengajuan tidak berhasil diubah!"
        ], 200);
      }
    } catch (\Exception $e) {
      DB::rollback();

      return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
    }
  }

  /* Payment Confirmation */
  public function paymentConfirmation(string $id)
  {
    $post = Reimbursement::find($id);
    $post->status = "done";

    $is_save = $post->save();

    if ($is_save == true) {
      return response()->json(['status' => true, 'pesan' => "Data pengajuan telah selesai dibayarkan!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data pengajuan gagal dibayarkan!"], 200);
    }
  }
}

<?php

namespace App\Http\Controllers;



use App\Models\Role;
use App\Models\User;
use App\Models\Courier;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  protected $get_menu;

  public function __construct()
  {
    $this->middleware('auth');

    $this->get_menu = get_menu_id('user');
  }

  /**
   * Display a listing of the resource.
   * @return Renderable
   */
  public function index(Request $request)
  {
    if (request()->ajax()) {
      $datas = User::orderBy('created_at', 'DESC');

      if (!empty($request->get('startdatetime_created_at')) && !empty($request->get('enddatetime_created_at'))) {
        $datas = $datas->where(function ($query) use ($request) {
          $query->whereBetween('created_at', [$request->get('startdatetime_created_at'), $request->get('enddatetime_created_at')]);
        });
      }

      $datas = $datas->whereNotIn('id', [Auth::user()->id])
        ->whereHas('role',  function ($query) {
          $query->whereNotIn('code', ['SA', 'CU']);
        })
        ->get();

      return DataTables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['name']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['nip']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['set_role']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('action', function ($data) {
          //get module akses
          $id_menu = get_menu_id('user');

          //detail
          $btn_detail = '';
          if (isAccess('detail', $id_menu, Auth::user()->role_id)) {
            $btn_detail = '<a class="dropdown-item" href="' . route('user.show', $data->id) . '"><i class="fas fa-info me-1"></i> Detail</a>';
          }

          //edit
          $btn_edit = '';
          if (isAccess('update', $id_menu, Auth::user()->role_id)) {
            $btn_edit = '<a class="dropdown-item" href="' . route('user.edit', $data->id) . '"><i class="fas fa-pencil-alt me-1"></i> Edit</a>';
          }

          //reset passwrod
          $btn_reset = '';
          if (isAccess('reset', $id_menu, Auth::user()->role_id)) {
            $btn_reset = '<a class="dropdown-item btn-reset" href="javascript:void(0)" data-id="' . $data->id . '" data-nama="' . $data->name . '"><i class="fas fa-undo-alt me-1"></i> Reset Password</a>';
          }

          //delete
          $btn_hapus = '';
          if (isAccess('delete', $id_menu, Auth::user()->role_id)) {
            $btn_hapus = '<a class="dropdown-item btn-hapus text-danger" href="javascript:void(0)" data-id="' . $data->id . '" data-nama="' . $data->name . '"><i class="fas fa-trash-alt me-1"></i> Hapus</a>';
          }

          return '
              <div class="d-inline-block">
                <a href="javascript:;" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </a>

                <div class="dropdown-menu dropdown-menu-end m-0" style="">
                  ' . $btn_detail . '
                  ' . $btn_edit . '
                  ' . $btn_reset . '
                  ' . $btn_hapus . '
                </div>
              </div>
          ';
        })
        ->addColumn('set_role', function ($data) {
          return $data->role->name ?? "";
        })
        ->rawColumns(['action', 'set_role'])
        ->addIndexColumn() //increment
        ->make(true);
    };

    $get_menu = $this->get_menu;

    if (isAccess('list', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.user.index', compact('get_menu'));
    } else {
      abort(419);
    }
  }

  public function rules($request)
  {
    $rule = [
      'name' => 'required|string|max:100',
      'nip' => 'required|string',
      'email' => 'required|email',
      'role_id' => 'required',
    ];
    $pesan = [
      'name.required' => 'Nama pengguna wajib diisi!',
      'nip.required' => 'Username wajib diisi!',
      'email.required' => 'Email pengguna wajib diisi!',
      'role_id.required' => 'Role akses wajib diisi!',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  /**
   * Show the form for creating a new resource.
   * @return Renderable
   */
  public function create()
  {
    if (Auth::user()->role->code != 'SA') {
      $roles = Role::orderBy('name')->whereNotIn('code', ['SA'])->get();
    } else {
      $roles = Role::orderBy('name')->get();
    }

    if (isAccess('create', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.user.create', compact('roles'));
    } else {
      abort(419);
    }
  }

  /**
   * Store a newly created resource in storage.
   * @param Request $request
   * @return Renderable
   */
  public function store(Request $request)
  {
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        // Validasi bahwa email dan NIP adalah unik
        $check_email = User::where('email', $request->email)
          ->first();

        if ($check_email != null) {
          return response()->json(['status' => false, 'pesan' => "Username sudah tersedia silahkan gunakan username yang berbeda!"], 200);
        }

        $check_nip = User::where('nip', $request->nip)
          ->first();

        if ($check_nip != null) {
          return response()->json(['status' => false, 'pesan' => "NIP sudah tersedia silahkan gunakan nip yang berbeda!"], 200);
        }

        $post = new User();
        $post->name = $request->name;
        $post->nip = $request->nip;
        $post->email = $request->email;
        $post->role_id = $request->role_id;
        $post->password = Hash::make('111111');

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json([
            'status' => true,
            'pesan' => "Data pengguna berhasil disimpan!"
          ], 200);
        } else {
          return response()->json([
            'status' => false,
            'pesan' => "Data pengguna tidak berhasil disimpan!"
          ], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
  }

  /**
   * Show the specified resource.
   * @param int $id
   * @return Renderable
   */
  public function show($id)
  {
    $data = User::find($id);
    $role = Role::orderBy('name')->get();

    if (isAccess('read', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.user.show', [
        'get_data' => $data,
        'role' => $role
      ]);
    } else {
      abort(419);
    }
  }

  /**
   * Show the form for editing the specified resource.
   * @param int $id
   * @return Renderable
   */
  public function edit($id)
  {
    $data = User::find($id);

    if (Auth::user()->role->code != 'SA') {
      $roles = Role::orderBy('name')->whereNotIn('code', ['SA'])->get();
    } else {
      $roles = Role::orderBy('name')->get();
    }

    if (isAccess('update', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.user.edit', ['get_data' => $data, 'roles' => $roles]);
    } else {
      abort(419);
    }
  }

  /**
   * Update the specified resource in storage.
   * @param Request $request
   * @param int $id
   * @return Renderable
   */
  public function update(Request $request, $id)
  {
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $post = User::find($id);

        // Validasi bahwa email adalah unik jika berubah
        if ($post->email != $request->email) {
          $check = User::where('email', $request->email)
            ->first();

          if ($check == null) {
            $post->email = $request->email;
          } else {
            return response()->json(['status' => false, 'pesan' => 'Email ' . $request->email . ' telah tersedia. Silahkan gunakan email lainnya.']);
          }
        }

        // Validasi bahwa NIP adalah unik jika berubah
        if ($post->nip != $request->nip) {
          $check = User::where('nip', $request->nip)
            ->first();

          if ($check == null) {
            $post->nip = $request->nip;
          } else {
            return response()->json(['status' => false, 'pesan' => 'NIP ' . $request->nip . ' telah tersedia. Silahkan gunakan NIP lainnya.']);
          }
        }

        $post->name = $request->name;
        $post->role_id = $request->role_id;

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json([
            'status' => true,
            'pesan' => "Data pengguna berhasil diubah!"
          ], 200);
        } else {
          return response()->json([
            'status' => false,
            'pesan' => "Data pengguna tidak berhasil diubah!"
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
   * @param int $id
   * @return Renderable
   */
  public function destroy($id)
  {
    $query_user = User::where('id', $id);

    $item_user = $query_user->first();

    if (File::exists($item_user->avatar)) {
      Storage::disk('public')->delete($item_user->avatar);
    }

    $hapus = $query_user->forceDelete();

    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data pengguna berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data pengguna tidak berhasil dihapus!"], 200);
    }
  }

  //reset password
  public function ResetPass($id)
  {
    $post = User::find($id);
    $post->password = Hash::make('111111');

    $simpan = $post->save();

    if ($simpan == true) {
      return response()->json([
        'status' => true,
        'pesan' => "Password Anda berhasil diubah menjadi <strong>111111</strong>!"
      ], 200);
    } else {
      return response()->json([
        'status' => false,
        'pesan' => "Password Anda tidak berhasil diubah!"
      ], 200);
    }
  }

  public function getUsersBySelect2(Request $request)
  {
    $search = $request->search;

    if ($search == '') {
      $data = User::orderby('name', 'asc')
        ->select('id', 'name', 'email')
        ->limit(10)
        ->get();
    } else {
      $data = User::orderby('name', 'asc')
        ->select('id', 'name', 'email')
        ->where('name', 'like', '%' . $search . '%')
        ->orwhere('username', 'like', '%' . $search . '%')
        ->orwhere('email', 'like', '%' . $search . '%')
        ->limit(10)
        ->get();
    }

    $response = array();
    foreach ($data as $item) {
      $response[] = array(
        "id" => $item->id,
        "text" => $item->name
      );
    }

    return response()->json($response);
  }
}

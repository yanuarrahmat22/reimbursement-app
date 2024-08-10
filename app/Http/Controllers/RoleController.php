<?php

namespace App\Http\Controllers;



use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
  protected $get_menu;

  public function __construct()
  {
    $this->middleware('auth');

    $this->get_menu = get_menu_id('role');
  }


  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if (request()->ajax()) {
      $datas = Role::select(['id', 'name', 'code', 'created_at', 'updated_at'])->get();

      return Datatables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['name']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['code']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('action', function ($data) {
          //get module akses
          $id_menu = get_menu_id('role');

          //detail
          $btn_role = "";
          if (isAccess('role', $id_menu, auth()->user()->role_id)) {
            $btn_role = '<button type="button" onclick="location.href=' . "'" . route('role.usermenuauthorization', $data->id) . "'" . ';" class="btn btn-sm btn-warning">Roles</button>';
          }

          //selalu bisa
          $btn_detail = '<a class="dropdown-item" href="' . route('role.show', $data->id) . '"><i class="fas fa-info me-1"></i> Detail</a>';

          //edit
          $btn_edit = '';
          if (isAccess('update', $id_menu, auth()->user()->role_id)) {
            $btn_edit = '<a class="dropdown-item" href="' . route('role.edit', $data->id) . '"><i class="fas fa-pencil-alt me-1"></i> Edit</a>';
          }

          //delete
          $btn_hapus = '';
          if (isAccess('delete', $id_menu, auth()->user()->role_id)) {
            $btn_hapus = '<a class="dropdown-item btn-hapus" href="javascript:void(0)" data-id="' . $data->id . '" data-nama="' . $data->name . '"><i class="fas fa-trash-alt me-1"></i> Hapus</a>';
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
                  ' . $btn_hapus . '
                </div>
              </div>
          ';
        })
        ->addColumn('set_tgl', function ($data) {
          return $data->updated_at != null ? Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->isoFormat('D MMMM YYYY HH:mm') : Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->isoFormat('D MMMM YYYY HH:mm');
        })
        ->addColumn('authorization', function ($data) {
          $id_menu = get_menu_id('role');

          //detail
          $btn_role = "";
          if (isAccess('role', $id_menu, auth()->user()->role_id)) {
            $btn_role = '<button type="button" onclick="location.href=' . "'" . route('role.usermenuauthorization', $data->id) . "'" . ';" class="btn btn-sm btn-warning">Roles</button>';
          }

          return $btn_role;
        })
        ->rawColumns(['action', 'set_tgl', 'authorization'])
        ->addIndexColumn() //increment
        ->make(true);
    };

    $get_menu = $this->get_menu;

    if (isAccess('list', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.role.index', compact('get_menu'));
    } else {
      abort(419);
    }
  }

  public function rules($request)
  {
    $rule = [
      'code' => 'required',
      'name' => 'required',
    ];

    $pesan = [
      'code.required' => 'Kode role akses wajib diisi!',
      'name.required' => 'Nama role akses wajib diisi!',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    if (isAccess('create', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.role.create');
    } else {
      abort(419);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $check = Role::where('code', $request->code)
          ->first();

        if ($check != null) {
          return response()->json(['status' => false, 'pesan' => "Kode role akses sudah tersedia silahkan gunakan kode role akses yang berbeda!"], 200);
        } else {
          $post = new Role();
          $post->name = $request->name;
          $post->code = $request->code;

          $simpan = $post->save();

          DB::commit();

          if ($simpan == true) {
            return response()->json([
              'status' => true,
              'pesan' => "Data role akses berhasil disimpan!"
            ], 200);
          } else {
            return response()->json([
              'status' => false,
              'pesan' => "Data role akses tidak berhasil disimpan!"
            ], 200);
          }
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function show(string $id)
  {
    $data = Role::find($id);

    if (isAccess('read', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.role.show', compact('data'));
    } else {
      abort(419);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function edit(string $id)
  {
    $data = Role::find($id);

    if (isAccess('update', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.role.edit', compact('data'));
    } else {
      abort(419);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, string $id)
  {
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $post = Role::find($id);

        if ($post->code != $request->code) {
          $check = Role::where('code', $request->code)
            ->first();

          if ($check == null) {
            $post->code = $request->code;
          } else {
            return response()->json([
              'status' => false,
              'pesan' => 'Kode role akses ' . $request->code . ' telah tersedia. Silahkan gunakan kode lainnya.'
            ]);
          }
        }

        $post->name = $request->name;

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json([
            'status' => true,
            'pesan' => "Data role akses berhasil disimpan!"
          ], 200);
        } else {
          return response()->json([
            'status' => false,
            'pesan' => "Data role akses tidak berhasil disimpan!"
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
   *
   * @param  \App\Models\Role  $role
   * @return \Illuminate\Http\Response
   */
  public function destroy(string $id)
  {
    $hapus = Role::where('id', $id)->delete();

    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data role akses berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data role akses tidak berhasil dihapus!"], 400);
    }
  }
}

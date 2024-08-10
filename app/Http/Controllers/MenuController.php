<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
  protected $get_menu;

  public function __construct()
  {
    $this->middleware('auth');

    $this->get_menu = get_menu_id('menu');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = Menu::where('upid', "0")
      ->orderBy('position', 'ASC')
      ->get();

    $get_menu = $this->get_menu;

    if (isAccess('list', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.menu.index', compact('data', 'get_menu'));
    } else {
      abort(419);
    }
  }

  public function rules($request)
  {
    $rule = [
      'name' => 'required|string|max:100',
      'code' => 'required|string|max:15',
      'link' => 'required|string',
      'icon' => 'required|string|max:50',
      'position' => 'required|numeric',
      'permission' => 'required',
    ];

    $pesan = [
      'name.required' => 'Nama menu wajib diisi!',
      'name.max' => 'Nama menu tidak boleh lebih dari 100 karakter!',
      'code.required' => 'Kode menu wajib diisi!',
      'code.max' => 'Kode menu tidak boleh lebih dari 15 karakter!',
      'link.required' => 'Link menu wajib diisi!',
      'icon.required' => 'Icon menu wajib diisi!',
      'icon.max' => 'Icon menu tidak boleh lebih dari 50 karakter!',
      'position.required' => 'Urutan menu wajib diisi!',
      'position.numeric' => 'Urutan menu wajib berisi angka!',
      'permission.required' => 'Action menu wajib diisi!',
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
    $menus = Menu::all()->where('upid', "0");

    if (isAccess('create', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.menu.create', compact('menus'));
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
        // Validasi bahwa code adalah unik
        $check = Menu::where('code', $request->code)
          ->first();

        if ($check != null) {
          return response()->json(['status' => false, 'pesan' => "Kode menu sudah tersedia silahkan gunakan kode menu yang berbeda!"], 200);
        } else {
          $post = new Menu();
          $post->upid = $request->post('upid') ?? 0;
          $post->code = $request->post('code');
          $post->name = $request->post('name');
          $post->link = $request->post('link');
          $post->description = $request->post('description');
          $post->icon = $request->post('icon');
          $post->position = $request->post('position');
          $post->permission = $request->post('permission');

          $simpan = $post->save();

          DB::commit();

          if ($simpan == true) {
            return response()->json([
              'status' => true,
              'pesan' => "Data menu berhasil disimpan!"
            ], 200);
          } else {
            return response()->json([
              'status' => false,
              'pesan' => "Data menu tidak berhasil disimpan!"
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
   * @param  \App\Models\Menu  $menu
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $data = Menu::find($id);

    if (isAccess('read', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.menu.show', compact('data'));
    } else {
      abort(419);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Menu  $menu
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $menus = Menu::all()->where('upid', "0");
    $data = Menu::find($id);

    if (isAccess('update', $this->get_menu, auth()->user()->role_id)) {
      return view('pages.menu.edit', compact('data', 'menus'));
    } else {
      abort(419);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Menu  $menu
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $post = Menu::find($id);

        // Validasi bahwa code adalah unik jika berubah
        if ($post->code != $request->code) {
          $check = Menu::where('code', $request->code)
            ->first();

          if ($check == null) {
            $post->code = $request->code;
          } else {
            return response()->json(['status' => false, 'pesan' => 'Kode menu ' . $request->code . ' telah tersedia. Silahkan gunakan kode lainnya.']);
          }
        }

        $post->upid = $request->post('upid') ?? 0;
        $post->name = $request->post('name');
        $post->link = $request->post('link');
        $post->description = $request->post('description');
        $post->icon = $request->post('icon');
        $post->position = $request->post('position');
        $post->permission = $request->post('permission');

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json([
            'status' => true,
            'pesan' => "Data menu berhasil disimpan!"
          ], 200);
        } else {
          return response()->json([
            'status' => false,
            'pesan' => "Data menu tidak berhasil disimpan!"
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
   * @param  \App\Models\Menu  $menu
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    try {
      // Ambil menu berdasarkan id
      $menu = Menu::find($id);

      if (!$menu) {
        return response()->json([
          'status' => false,
          'pesan' => "Menu tidak ditemukan!"
        ], 404);
      }

      // Periksa apakah menu adalah menu indukan atau submenu
      if ($menu->upid == "0") {
        // Menu adalah menu indukan, hapus juga submenu yang terkait
        Menu::where('upid', $id)->delete();
      }

      // Hapus menu
      $hapus = $menu->delete();

      if ($hapus) {
        return response()->json([
          'status' => true,
          'pesan' => "Data menu berhasil dihapus!"
        ], 200);
      } else {
        return response()->json([
          'status' => false,
          'pesan' => "Data menu tidak berhasil dihapus!"
        ], 400);
      }
    } catch (QueryException $e) {
      // Tangani pengecualian jika terjadi error pada query
      return response()->json([
        'status' => false,
        'pesan' => "Terjadi kesalahan saat menghapus data menu: " . $e->getMessage()
      ], 500);
    } catch (\Exception $e) {
      // Tangani pengecualian umum jika terjadi error lainnya
      return response()->json([
        'status' => false,
        'pesan' => "Terjadi kesalahan: " . $e->getMessage()
      ], 500);
    }
  }

  public function sort()
  {
    // $sortData = array();
    $sort = 1;
    foreach (request('main') as $key => $main) {
      if (is_array($main)) {
        $no = 1;
        foreach ($main as $a => $b) {
          $sortData[$b]['parent'] = $key;
          $sortData[$b]['sort'] = $no;
          $no++;
        }
      } else {
        // echo $main."<br>";
        $sortData[$main]['parent'] = "0";
        $sortData[$main]['sort'] = $sort;
        $sort++;
      }
    }

    foreach ($sortData as $id => $data) {
      $id = str_replace("mdl-", "", $id);
      $parent = str_replace("mdl-", "", $data['parent']);

      $set =  Menu::find($id);
      $set->upid = $parent;
      $set->save();
    }
  }
}

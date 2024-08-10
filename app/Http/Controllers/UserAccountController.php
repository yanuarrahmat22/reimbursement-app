<?php

namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserAccountController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function rules($request)
  {
    $rule = [
      'name' => 'required|string|max:100',
      'email' => 'required|string|email',
      'avatar' => 'max:2048|mimes:png,jpeg,gif|sometimes|nullable',
    ];

    $pesan = [
      'name.required' => 'Nama pengguna tidak boleh kosong!',
      'email.required' => 'Email pengguna tidak boleh kosong!',
      'avatar.max' => 'File tidak boleh lebih dari 2Mb!',
      'avatar.mimes' => 'File format hanya .png, .jpeg, atau .gif!'
    ];

    return Validator::make($request, $rule, $pesan);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function userProfileAccount()
  {
    $item_user = User::findOrFail(Auth::user()->id);

    return view('pages.user-account.profile', [
      'user_session' => Auth::user()->id,
      'item_user' => $item_user
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function storeUserProfileAccount(Request $request, $id)
  {
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $post = User::find($id);

        if ($post->email != $request->email) {
          $check = User::where('email', $request->email)
            ->first();

          if ($check == null) {
            $post->email = $request->email;
          } else {
            return response()->json(['status' => false, 'pesan' => 'Email ' . $request->email . ' telah tersedia. Silahkan gunakan email lainnya.']);
          }
        }

        $post->name = $request->post('name');

        if ($request->hasFile('avatar')) {
          // if (Storage::disk('public')->exists($post->avatar)) {
          //   Storage::disk('public')->delete($post->avatar);
          // }

          $post->avatar = $request->file('avatar')->store('user-avatar', 'public');
          // $post->avatar_originalfile = $request->file('avatar')->getClientOriginalName();
          // $post->avatar_originalmimetype = $request->file('avatar')->getClientOriginalExtension();
          // $post->avatar_mimetype = $request->file('avatar')->getMimeType();
        }

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json([
            'status' => true,
            'pesan' => "Profile Anda berhasil diubah!"
          ], 200);
        } else {
          return response()->json([
            'status' => false,
            'pesan' => "Profile Anda tidak berhasil diubah!"
          ], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
  }

  public function rules_password($request)
  {
    $rule = [
      'current_password' => ['required', new MatchOldPassword],
      'new_password' => 'required|string|min:6|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
      'new_confirm_password' => 'required|string|min:6|same:new_password|required_with:new_password'
    ];

    $pesan = [
      'current_password.current_password' => 'Wajib mengisikan password saat ini!',
      'new_password.required' => 'New password wajib diisi!',
      'new_password.min' => 'Minimal panjang password baru yaitu 6 karakter!',
      'new_confirm_password.required' => "Confirm new password wajib diisi!",
      'new_confirm_password.min' => "Confirm new password minimal 6 karakter!",
      'new_confirm_password.same' => "Confirm new password tidak sesuai dengan new password!"
    ];

    return Validator::make($request, $rule, $pesan);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function userPasswordAccount()
  {
    $user_session = Auth::user()->id;
    $item_user = User::findOrFail($user_session);

    return view('pages.user-account.password', [
      'user_session' => $user_session,
      'item_user' => $item_user
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function storeUserPasswordAccount(Request $request, $id)
  {
    $validator = $this->rules_password($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $post = User::find($id);
        $post->password = Hash::make($request->new_password);

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json([
            'status' => true,
            'pesan' => "Password Anda berhasil diubah, pastikan Anda login dengan password <strong>" . $request->new_password . "</strong>!"
          ], 200);
        } else {
          return response()->json([
            'status' => false,
            'pesan' => "Password Anda tidak berhasil diubah!"
          ], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
  }
}

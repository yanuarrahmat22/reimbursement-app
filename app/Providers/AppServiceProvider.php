<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\UserMenuAuthorization;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Guard $auth): void
  {
    config(['app.locale' => 'id']);
    Carbon::setLocale('id');
    date_default_timezone_set('Asia/Jakarta');

    View::composer('*', function ($view) use ($auth) {
      $menus = Menu::where('upid', '0')->orderBy('position', 'ASC')->get();
      $menu = new Menu();
      $userMenuAuthorization = new UserMenuAuthorization();

      $view->with([
        'AuthData' => $auth->user(),
        'menus' => $menus,
        'menu' => $menu,
        'userMenuAuthorization' => $userMenuAuthorization
      ]);
    });

    Paginator::useBootstrap();

    Validator::extend('required_if_null', function ($attribute, $value, $parameters, $validator) {
      $otherField = $parameters[0];
      $otherValue = $validator->getData()[$otherField];

      // Jika nilai pada kolom lain adalah null, maka input harus required
      return $otherValue === null ? !empty($value) : true;
    });
  }
}

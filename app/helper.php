<?php

use App\Models\Menu;
use App\Models\Role;
use App\Models\UserMenuAuthorization;
use Illuminate\Support\Facades\Request;

if (!function_exists('check')) {
  function check($model)
  {
    return $model . " activated";
  }
}

if (!function_exists('isAccess')) {
  function isAccess($act, $menu, $role)
  {
    $authorization = new UserMenuAuthorization;
    $action = $authorization->where('role_id', $role)->where('menu_id', $menu)->where('status', TRUE)->first();

    //tidak memiliki akses
    if ($action == NULL) {
      return FALSE;
    }

    if (strpos($action->permission_given, $act) !== false) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}

if (!function_exists('get_menu_id')) {
  function get_menu_id($menu)
  {
    $menuData = Menu::where('link', $menu)->first();

    // dd($menu);

    return $menuData->id;
  }
}

if (!function_exists('isSelected')) {
  function isSelected($a, $b)
  {
    if ($a == $b) {
      echo "selected";
    }
  }
}

if (!function_exists('rupiah_format')) {
  function rupiah_format($nominal)
  {
    return number_format($nominal, 0, 0, ".");
  }
}

if (!function_exists('IsSelected')) {
  function IsSelected($param_one, $param_two)
  {
    return ($param_one == $param_two) ? "SELECTED" : null;
  }
}

if (!function_exists('activeMenu')) {
  function activeMenu($uri = '')
  {
    $active = '';

    /* if (Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri . '*')) {
      $active = 'active';
    } */

    if (Request::is($uri . '/*') || Request::is($uri)) {
      $active = 'active';
    }

    return $active;
  }
}

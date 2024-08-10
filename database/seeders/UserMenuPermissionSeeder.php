<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use App\Models\UserMenuAuthorization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserMenuPermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Model::unguard();

    // BUAT SEEDER UNTUK MENU DASHBOARD
    $menu_dashboard = Menu::create([
      'upid' => '0',
      'code' => 'HOME',
      'name' => 'Dashboard',
      'link' => 'home',
      'description' => '-',
      'icon' => 'fa fa-home',
      'permission' => 'list,read',
      'position' => 1,
    ]);

    // BUAT SEEDER UNTUK MENU REIMBURSEMENT
    $menu_parent_reimbursement = Menu::create([
      'upid' => '0',
      'code' => 'MRI',
      'name' => 'Data Reimbursement',
      'link' => 'reimbursement',
      'description' => '-',
      'icon' => 'fas fa-money-check-alt',
      'permission' => 'create,read,update,delete,list,approval,payment-confirmation',
      'position' => 2,
    ]);

    // BUAT SEEDER UNTUK MENU MANAGEMENT
    $menu_parent_menu = Menu::create([
      'upid' => '0',
      'code' => 'MOD',
      'name' => 'Data Menu',
      'link' => 'menu',
      'description' => '-',
      'icon' => 'fa fa-list',
      'permission' => 'create,read,update,delete,list',
      'position' => 3,
    ]);

    // BUAT SEEDER UNTUK MENU USER MANAGEMENT
    $menu_parent_user_management = Menu::create([
      'upid' => '0',
      'code' => 'USM',
      'name' => 'User Management',
      'link' => 'user-management',
      'description' => '-',
      'icon' => 'fa fa-users',
      'permission' => 'read,list',
      'position' => 4,
    ]);

    $menu_child_user_management_role = Menu::create([
      'upid' => $menu_parent_user_management->id,
      'code' => 'PRIV',
      'name' => 'Role Akses',
      'link' => 'role',
      'description' => '-',
      'icon' => 'fa fa-cog',
      'permission' => 'create,update,delete,read,detail,list,roles',
      'position' => 1,
    ]);

    $menu_child_user_management_user = Menu::create([
      'upid' => $menu_parent_user_management->id,
      'code' => 'USR',
      'name' => 'Data Pengguna',
      'link' => 'user',
      'description' => '-',
      'icon' => 'fa fa-user',
      'permission' => 'create,read,update,delete,import,export,detail,reset,list',
      'position' => 2,
    ]);


    /* CREATE ROLE */
    $role_administrator = Role::create([
      'name' => 'Administrator',
      'code' => 'SA'
    ]);

    $role_director = Role::create([
      'name' => 'Director',
      'code' => 'DR'
    ]);

    $role_finance = Role::create([
      'name' => 'Finance',
      'code' => 'FI'
    ]);

    $role_staff = Role::create([
      'name' => 'Staff',
      'code' => 'ST'
    ]);

    /* CREATE PERMISSION MENU SETIAP ROLE */
    // UNTUK administrator
    UserMenuAuthorization::create([
      'role_id' => $role_administrator->id,
      'menu_id' => $menu_dashboard->id,
      'permission_given' => 'list,read',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_administrator->id,
      'menu_id' => $menu_parent_reimbursement->id,
      'permission_given' => 'create,read,update,delete,list,approval,payment-confirmation',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_administrator->id,
      'menu_id' => $menu_parent_menu->id,
      'permission_given' => 'create,read,update,delete,list',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_administrator->id,
      'menu_id' => $menu_parent_user_management->id,
      'permission_given' => 'read,list',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_administrator->id,
      'menu_id' => $menu_child_user_management_role->id,
      'permission_given' => 'create,update,delete,read,detail,list,roles',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_administrator->id,
      'menu_id' => $menu_child_user_management_user->id,
      'permission_given' => 'create,read,update,delete,import,export,detail,reset,list',
      'status' => true
    ]);

    // UNTUK DIRECTOR
    UserMenuAuthorization::create([
      'role_id' => $role_director->id,
      'menu_id' => $menu_dashboard->id,
      'permission_given' => 'list,read',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_director->id,
      'menu_id' => $menu_parent_reimbursement->id,
      'permission_given' => 'create,read,update,delete,list,approval',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_director->id,
      'menu_id' => $menu_parent_user_management->id,
      'permission_given' => 'read,list',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_director->id,
      'menu_id' => $menu_child_user_management_user->id,
      'permission_given' => 'create,read,update,delete,import,export,detail,reset,list',
      'status' => true
    ]);

    // UNTUK FINANCE
    UserMenuAuthorization::create([
      'role_id' => $role_finance->id,
      'menu_id' => $menu_dashboard->id,
      'permission_given' => 'list,read',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_finance->id,
      'menu_id' => $menu_parent_reimbursement->id,
      'permission_given' => 'create,read,update,delete,list,payment-confirmation',
      'status' => true
    ]);

    // UNTUK STAFF
    UserMenuAuthorization::create([
      'role_id' => $role_staff->id,
      'menu_id' => $menu_dashboard->id,
      'permission_given' => 'list,read',
      'status' => true
    ]);

    UserMenuAuthorization::create([
      'role_id' => $role_staff->id,
      'menu_id' => $menu_parent_reimbursement->id,
      'permission_given' => 'create,read,update,delete,list',
      'status' => true
    ]);

    /* CREATE USER */
    User::factory()->create([
      'name' => 'Administrator',
      'email' => 'administrator@example.com',
      'password' => Hash::make('000000'),
      'nip' => '0000',
      'role_id' => $role_administrator
    ]);

    User::factory()->create([
      'name' => 'Doni',
      'email' => 'doni@example.com',
      'password' => Hash::make('123456'),
      'nip' => '1234',
      'role_id' => $role_director
    ]);

    User::factory()->create([
      'name' => 'Dono',
      'email' => 'dono@example.com',
      'password' => Hash::make('123456'),
      'nip' => '1235',
      'role_id' => $role_finance
    ]);

    User::factory()->create([
      'name' => 'Dona',
      'email' => 'dona@example.com',
      'password' => Hash::make('123456'),
      'nip' => '1236',
      'role_id' => $role_staff
    ]);
  }
}

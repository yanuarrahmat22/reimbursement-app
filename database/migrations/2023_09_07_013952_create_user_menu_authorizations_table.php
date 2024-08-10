<?php

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('user_menu_authorizations', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignIdFor(Role::class);
      $table->foreignIdFor(Menu::class);
      $table->longText('permission_given')->nullable();
      $table->boolean('status')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('user_menu_authorizations');
  }
};

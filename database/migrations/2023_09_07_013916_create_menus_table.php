<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('menus', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('upid', 36)->default('0');
      $table->string('code', 15);
      $table->string('name');
      $table->string('link');
      $table->text('description')->nullable();
      $table->string('icon');
      $table->integer('position');
      $table->longText('permission');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('menus');
  }
};

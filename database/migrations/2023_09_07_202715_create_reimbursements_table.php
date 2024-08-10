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
    Schema::create('reimbursements', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('user_created');
      $table->date('date_created');
      $table->string('name');
      $table->text('description')->nullable();
      $table->text('file')->nullable();
      $table->string('status', 100);
      $table->uuid('user_approved')->nullable();
      $table->date('date_approved')->nullable();
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('user_created')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('user_approved')->references('id')->on('users')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('reimbursements', function (Blueprint $table) {
      $table->dropForeign(['user_created']);
      $table->dropForeign(['user_approved']);
    });

    Schema::dropIfExists('reimbursements');
  }
};

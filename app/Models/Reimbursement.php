<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reimbursement extends Model
{
  use HasFactory, Uuid, SoftDeletes;
  public $incrementing = false;

  protected $fillable = [
    'user_created',
    'date_created',
    'name',
    'description',
    'file',
    'status',
    'user_approved',
    'date_approved'
  ];

  public function usercreated()
  {
    return $this->belongsTo(User::class, 'user_created', 'id')->withDefault();
  }

  public function userapproved()
  {
    return $this->belongsTo(User::class, 'user_approved', 'id')->withDefault();
  }
}

<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
  use HasFactory, Uuid, SoftDeletes;

  public $incrementing = false;

  protected $fillable = [
    'upid',
    'code',
    'name',
    'link',
    'description',
    'icon',
    'position',
    'permission'
  ];

  public function menu()
  {
    // return $this->hasOne(Menu::class, 'id', 'upid')->orderBy('position', 'ASC');
    return $this->belongsTo(Menu::class, 'upid', 'id')->withDefault();
  }

  public function menus(): HasMany
  {
    return $this->hasMany(Menu::class, 'upid', 'id')->orderBy('position', 'ASC');
  }
}

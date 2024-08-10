<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMenuAuthorization extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;

    protected $fillable = [
        'role_id',
        'menu_id',
        'permission_given',
        'status'
    ];

    public function menu(): HasOne
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }
}

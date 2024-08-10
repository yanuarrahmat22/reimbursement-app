<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'code',
        'name'
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'id', 'role_id');
    }
}

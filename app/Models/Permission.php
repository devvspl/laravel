<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'permission_key',
        'permission_group_id',
        'status',
        'created_by',
        'updated_by',
        'guard_name',
        'created_at',
        'updated_at',
    ];
}

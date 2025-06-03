<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_group_id');
    }
}
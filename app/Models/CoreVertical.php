<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreVertical extends Model
{
    protected $connection = 'hrims';
    protected $table = 'core_verticals';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'vertical_name', 'vertical_code', 'effective_date', 'is_active'];
}

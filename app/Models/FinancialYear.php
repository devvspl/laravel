<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    protected $table = 'financialyear';
    protected $primaryKey = 'YearId';
    public $timestamps = false;

    protected $fillable = [
        'Year', 'y1', 'y2', 'FromDate', 'Todate', 'Status', 'CrBy'
    ];
}

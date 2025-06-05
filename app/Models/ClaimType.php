<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimType extends Model
{
    protected $connection = 'expense';
    protected $table = 'claimtype';
    protected $primaryKey = 'ClaimId';
    public $timestamps = false;
    protected $fillable = ['ClaimId', 'cgId', 'ClaimName', 'ClaimCode', 'ClaimStatus', 'ClaimCrBy'];
}

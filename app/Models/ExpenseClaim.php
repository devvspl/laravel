<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseClaim extends Model
{
    protected $connection = 'expense';
    protected $primaryKey = 'ExpId';
    public $timestamps = false;
    protected $fillable = [
        'ExpId',
        'ClaimId',
        'activity_type',
        'odomtr_opening',
        'opening_filepath',
        'odomtr_closing',
        'closing_filepath',
        'offline_opening_filepath',
        'offline_clossing_filepath',
        'TotKm',
        'WType',
        'v_verify',
        'c_verify',
        'RatePerKM',
        'With_Driver',
        'VType',
        'Img_1',
        'Img_2',
        'Img_3',
        'Img_4',
        'Img_5',
        'Amount',
        'Remark',
        'DateEntryRemark',
        'ClaimStatus',
        'ClaimAtStep',
        'Rmk',
        'CrBy',
        'CrDate',
        'BillDate',
        'ClaimMonth',
        'ClaimYearId',
        'FilledBy',
        'FilledTAmt',
        'FilledDate',
        'FilledOkay',
        'FilOkDenyRemark',
        'Opening_Remark',
        'Closing_Remark',
        'Other_Remark',
        'VerifyBy',
        'VerifyTAmt',
        'VerifyTRemark',
        'VerifyDate',
        'ApprBy',
        'ApprTAmt',
        'ApprTRemark',
        'ApprDate',
        'FinancedBy',
        'FinancedTAmt',
        'FinancedTRemark',
        'FinancedDate',
        'AttachTo',
        'RtnBy',
        'offline_exp_id',
        'Img1_offline',
        'Img2_offline',
        'Img3_offline',
        'Pdf1_offline',
        'Pdf2_offline',
        'App_Odometer',
        'temp_punch_id'
    ];
    public static function tableName(): string
    {
        $yearId = session('year_id');
        return 'expense.y' . $yearId . '_expenseclaims';
    }
}
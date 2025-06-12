<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function overview()
    {
        // // Cache the dashboard data for 10 minutes to reduce database load
        // $cacheKey = 'dashboard_overview';
        // $data = Cache::remember($cacheKey, now()->addMinutes(10), function () {
        //     // Query 1: Combined claim status counts and total
        //     $claimStatusCounts = DB::table('y6_expenseclaims')
        //         ->selectRaw("IFNULL(ClaimStatus, 'Total') as ClaimStatus, COUNT(*) as TotalCount")
        //         ->groupByRaw('ClaimStatus WITH ROLLUP')
        //         ->get();

           

        //     // Query 2: Total financed amount by claim type
        //     $claimTypeTotals = DB::table('y6_expenseclaims')
        //         ->join('claimtype', 'claimtype.ClaimId', '=', 'y6_expenseclaims.ClaimId')
        //         ->select('claimtype.ClaimName', DB::raw('SUM(y6_expenseclaims.FinancedTAmt) as TotalFinancedAmount'))
        //         ->groupBy('claimtype.ClaimId', 'claimtype.ClaimName')
        //         ->get();

        //              dd($claimTypeTotals);

        //     // Query 3: Total financed amount by claim month
        //     $monthlyTotals = DB::table('y6_expenseclaims')
        //         ->select('ClaimMonth', DB::raw('SUM(FinancedTAmt) as TotalFinancedAmount'))
        //         ->where('ClaimMonth', '!=', 0)
        //         ->groupBy('ClaimMonth')
        //         ->get();

        //     // Query 4: Total financed amount by department for Claim Years 5 and 6
        //     $departmentTotals = DB::table(DB::raw('(SELECT ExpId, CrBy, ClaimYearId, FinancedTAmt FROM y5_expenseclaims UNION ALL SELECT ExpId, CrBy, ClaimYearId, FinancedTAmt FROM y6_expenseclaims) as e'))
        //         ->join('hrims.hrm_employee_general as gen', 'gen.EmployeeID', '=', 'e.CrBy')
        //         ->join('hrims.core_departments as dep', 'gen.DepartmentId', '=', 'dep.id')
        //         ->select(
        //             'dep.department_name',
        //             DB::raw('SUM(CASE WHEN e.ClaimYearId = 5 THEN e.FinancedTAmt ELSE 0 END) as TotalFinancedTAmt_Y5'),
        //             DB::raw('SUM(CASE WHEN e.ClaimYearId = 6 THEN e.FinancedTAmt ELSE 0 END) as TotalFinancedTAmt_Y6')
        //         )
        //         ->whereNotNull('dep.department_name')
        //         ->groupBy('dep.department_name')
        //         ->orderBy('dep.department_name')
        //         ->get();

        //     // Query 5: Year-over-year expense comparison
        //     $yearlyComparison = DB::table(DB::raw('(SELECT ClaimYearId, FinancedTAmt FROM y5_expenseclaims UNION ALL SELECT ClaimYearId, FinancedTAmt FROM y6_expenseclaims) as e'))
        //         ->selectRaw('
        //             SUM(CASE WHEN ClaimYearId = 6 THEN FinancedTAmt ELSE 0 END) as CY_Expense,
        //             SUM(CASE WHEN ClaimYearId = 5 THEN FinancedTAmt ELSE 0 END) as PY_Expense,
        //             SUM(CASE WHEN ClaimYearId = 6 THEN FinancedTAmt ELSE 0 END) - 
        //             SUM(CASE WHEN ClaimYearId = 5 THEN FinancedTAmt ELSE 0 END) as Variance,
        //             (SUM(CASE WHEN ClaimYearId = 6 THEN FinancedTAmt ELSE 0 END) - 
        //             SUM(CASE WHEN ClaimYearId = 5 THEN FinancedTAmt ELSE 0 END)) / 
        //             NULLIF(SUM(CASE WHEN ClaimYearId = 5 THEN FinancedTAmt ELSE 0 END), 0) * 100 as Variance_Percentage
        //         ')
        //         ->whereIn('ClaimYearId', [5, 6])
        //         ->first();

        //     // Map ClaimStatus to dashboard card labels
        //     $statusMap = [
        //         'draft' => 'Draft',
        //         'deactivated' => 'Deactivated',
        //         'submitted' => 'Submitted',
        //         'filled' => 'Filled',
        //         'verified' => 'Verified',
        //         'approved' => 'Approved',
        //         'financed' => 'Financed',
        //         'total' => 'Total Expense'
        //     ];

        //     $cardData = [];
        //     foreach ($claimStatusCounts as $status) {
        //         $key = strtolower($status->ClaimStatus);
        //         if (isset($statusMap[$key])) {
        //             $cardData[$statusMap[$key]] = $status->TotalCount;
        //         }
        //     }

        //     return compact('cardData', 'claimTypeTotals', 'monthlyTotals', 'departmentTotals', 'yearlyComparison');
        // });

        return view('admin.overview');
    }


}
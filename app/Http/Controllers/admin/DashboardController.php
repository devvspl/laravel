<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function overview()
    {
        // // Query 1: Count of Claims by Claim Status
        // $claimStatusCounts = DB::select("
        //     SELECT ClaimStatus, COUNT(*) AS TotalCount 
        //     FROM y6_expenseclaims 
        //     GROUP BY ClaimStatus 
        //     UNION ALL 
        //     SELECT 'Total' AS ClaimStatus, COUNT(*) AS TotalCount 
        //     FROM y6_expenseclaims
        // ");

        // // Query 2: Total Financed Amount by Claim Type
        // $claimTypeTotals = DB::select("
        //     SELECT claimtype.ClaimName, SUM(y6_expenseclaims.FinancedTAmt) AS TotalFinancedAmount 
        //     FROM y6_expenseclaims 
        //     INNER JOIN claimtype ON claimtype.ClaimId = y6_expenseclaims.ClaimId 
        //     GROUP BY claimtype.ClaimId, claimtype.ClaimName
        // ");

        // // Query 3: Total Financed Amount by Claim Month
        // $monthlyTotals = DB::select("
        //     SELECT y6_expenseclaims.ClaimMonth, SUM(y6_expenseclaims.FinancedTAmt) AS TotalFinancedAmount 
        //     FROM y6_expenseclaims 
        //     WHERE y6_expenseclaims.ClaimMonth != 0 
        //     GROUP BY y6_expenseclaims.ClaimMonth
        // ");

        // // Query 4: Total Financed Amount by Department for Claim Years 5 and 6
        // $departmentTotals = DB::select("
        //     SELECT dep.department_name, 
        //            SUM(CASE WHEN e.ClaimYearId = 5 THEN e.FinancedTAmt ELSE 0 END) AS TotalFinancedTAmt_Y5, 
        //            SUM(CASE WHEN e.ClaimYearId = 6 THEN e.FinancedTAmt ELSE 0 END) AS TotalFinancedTAmt_Y6 
        //     FROM (
        //         SELECT ExpId, CrBy, ClaimYearId, FinancedTAmt 
        //         FROM y5_expenseclaims 
        //         UNION ALL 
        //         SELECT ExpId, CrBy, ClaimYearId, FinancedTAmt 
        //         FROM y6_expenseclaims
        //     ) e 
        //     LEFT JOIN hrims.hrm_employee_general gen ON gen.EmployeeID = e.CrBy 
        //     LEFT JOIN hrims.core_departments dep ON gen.DepartmentId = dep.id 
        //     WHERE dep.department_name IS NOT NULL 
        //     GROUP BY dep.department_name 
        //     ORDER BY dep.department_name ASC
        // ");

        // // Query 5: Year-over-Year Expense Comparison
        // $yearlyComparison = DB::select("
        //     SELECT
        //         SUM(CASE WHEN e.ClaimYearId = 6 THEN e.FinancedTAmt ELSE 0 END) AS CY_Expense, 
        //         SUM(CASE WHEN e.ClaimYearId = 5 THEN e.FinancedTAmt ELSE 0 END) AS PY_Expense,
        //         SUM(CASE WHEN e.ClaimYearId = 6 THEN e.FinancedTAmt ELSE 0 END) - 
        //         SUM(CASE WHEN e.ClaimYearId = 5 THEN e.FinancedTAmt ELSE 0 END) AS Variance,
        //         (SUM(CASE WHEN e.ClaimYearId = 6 THEN e.FinancedTAmt ELSE 0 END) - 
        //         SUM(CASE WHEN e.ClaimYearId = 5 THEN e.FinancedTAmt ELSE 0 END)) / 
        //         NULLIF(SUM(CASE WHEN e.ClaimYearId = 5 THEN e.FinancedTAmt ELSE 0 END), 0) * 100 AS Variance_Percentage 
        //     FROM 
        //         (SELECT ExpId, CrBy, ClaimYearId, FinancedTAmt FROM y5_expenseclaims
        //          UNION ALL
        //          SELECT ExpId, CrBy, ClaimYearId, FinancedTAmt FROM y6_expenseclaims) e
        //     WHERE
        //         e.ClaimYearId IN (5, 6)
        // ")[0]; // Single row result

        // // Map ClaimStatus to dashboard card labels (adjust based on actual ClaimStatus values)
        // $statusMap = [
        //     'draft' => 'Draft',
        //     'deactivated' => 'Deactivated',
        //     'submitted' => 'Submitted',
        //     'filled' => 'Filled',
        //     'verified' => 'Verified',
        //     'approved' => 'Approved',
        //     'financed' => 'Financed',
        //     'total' => 'Total Expense'
        // ];

        // // Prepare data for cards
        // $cardData = [];
        // foreach ($claimStatusCounts as $status) {
        //     $key = strtolower($status->ClaimStatus);
        //     if (isset($statusMap[$key])) {
        //         $cardData[$statusMap[$key]] = $status->TotalCount;
        //     }
        // }

        // // Pass data to the view
        // return view('admin.overview', compact(
        //     'cardData',
        //     'claimTypeTotals',
        //     'monthlyTotals',
        //     'departmentTotals',
        //     'yearlyComparison'
        // ));

    }
    public function filter(Request $request)
    {
        $month = $request->input('month');
        $claimType = $request->input('claim_type');
        $department = $request->input('department');
        $claimStatus = $request->input('claim_status');

        // Base query for claim status counts
        $statusQuery = "SELECT ClaimStatus, COUNT(*) AS TotalCount 
                    FROM y6_expenseclaims 
                    WHERE 1=1";
        $statusParams = [];

        // Base query for claim type totals
        $claimTypeQuery = "SELECT claimtype.ClaimName, SUM(y6_expenseclaims.FinancedTAmt) AS TotalFinancedAmount 
                       FROM y6_expenseclaims 
                       INNER JOIN claimtype ON claimtype.ClaimId = y6_expenseclaims.ClaimId 
                       WHERE 1=1";
        $claimTypeParams = [];

        // Base query for department totals
        $departmentQuery = "SELECT dep.department_name, 
                              SUM(CASE WHEN e.ClaimYearId = 5 THEN e.FinancedTAmt ELSE 0 END) AS TotalFinancedTAmt_Y5, 
                              SUM(CASE WHEN e.ClaimYearId = 6 THEN e.FinancedTAmt ELSE 0 END) AS TotalFinancedTAmt_Y6 
                       FROM (
                           SELECT ExpId, CrBy, ClaimYearId, FinancedTAmt 
                           FROM y5_expenseclaims 
                           UNION ALL 
                           SELECT ExpId, CrBy, ClaimYearId, FinancedTAmt 
                           FROM y6_expenseclaims
                       ) e 
                       LEFT JOIN hrims.hrm_employee_general gen ON gen.EmployeeID = e.CrBy 
                       LEFT JOIN hrims.core_departments dep ON gen.DepartmentId = dep.id 
                       WHERE dep.department_name IS NOT NULL";
        $departmentParams = [];

        // Apply filters
        if ($month) {
            $monthNum = array_search(ucfirst($month), [
                'jan' => 'January',
                'feb' => 'February',
                'mar' => 'March',
                'apr' => 'April',
                'may' => 'May',
                'jun' => 'June',
                'jul' => 'July',
                'aug' => 'August',
                'sep' => 'September',
                'oct' => 'October',
                'nov' => 'November',
                'dec' => 'December'
            ]) + 1;
            $statusQuery .= " AND ClaimMonth = ?";
            $claimTypeQuery .= " AND ClaimMonth = ?";
            $departmentQuery .= " AND e.ClaimMonth = ?";
            $statusParams[] = $monthNum;
            $claimTypeParams[] = $monthNum;
            $departmentParams[] = $monthNum;
        }

        if ($claimType) {
            $statusQuery .= " AND ClaimId IN (SELECT ClaimId FROM claimtype WHERE ClaimName = ?)";
            $claimTypeQuery .= " AND claimtype.ClaimName = ?";
            $departmentQuery .= " AND e.ClaimId IN (SELECT ClaimId FROM claimtype WHERE ClaimName = ?)";
            $statusParams[] = $claimType;
            $claimTypeParams[] = $claimType;
            $departmentParams[] = $claimType;
        }

        if ($department) {
            $statusQuery .= " AND CrBy IN (SELECT EmployeeID FROM hrims.hrm_employee_general WHERE DepartmentId IN (SELECT id FROM hrims.core_departments WHERE department_name = ?))";
            $claimTypeQuery .= " AND CrBy IN (SELECT EmployeeID FROM hrims.hrm_employee_general WHERE DepartmentId IN (SELECT id FROM hrims.core_departments WHERE department_name = ?))";
            $departmentQuery .= " AND dep.department_name = ?";
            $statusParams[] = $department;
            $claimTypeParams[] = $department;
            $departmentParams[] = $department;
        }

        if ($claimStatus) {
            $statusQuery .= " AND ClaimStatus = ?";
            $claimTypeQuery .= " AND ClaimStatus = ?";
            $departmentQuery .= " AND e.ClaimStatus = ?";
            $statusParams[] = $claimStatus;
            $claimTypeParams[] = $claimStatus;
            $departmentParams[] = $claimStatus;
        }

        // Add total for status query
        $statusQuery .= " GROUP BY ClaimStatus 
                      UNION ALL 
                      SELECT 'Total' AS ClaimStatus, COUNT(*) AS TotalCount 
                      FROM y6_expenseclaims 
                      WHERE 1=1";
        if ($month)
            $statusQuery .= " AND ClaimMonth = ?";
        if ($claimType)
            $statusQuery .= " AND ClaimId IN (SELECT ClaimId FROM claimtype WHERE ClaimName = ?)";
        if ($department)
            $statusQuery .= " AND CrBy IN (SELECT EmployeeID FROM hrims.hrm_employee_general WHERE DepartmentId IN (SELECT id FROM hrims.core_departments WHERE department_name = ?))";
        if ($claimStatus)
            $statusQuery .= " AND ClaimStatus = ?";
        $statusParams = array_merge($statusParams, array_filter([$month ? $monthNum : null, $claimType, $department, $claimStatus]));

        // Group and order for other queries
        $claimTypeQuery .= " GROUP BY claimtype.ClaimId, claimtype.ClaimName";
        $departmentQuery .= " GROUP BY dep.department_name ORDER BY dep.department_name ASC";

        // Execute queries
        $claimStatusCounts = DB::select($statusQuery, $statusParams);
        $claimTypeTotals = DB::select($claimTypeQuery, $claimTypeParams);
        $departmentTotals = DB::select($departmentQuery, $departmentParams);

        // Map card data
        $statusMap = [
            'draft' => 'Draft',
            'deactivated' => 'Deactivated',
            'submitted' => 'Submitted',
            'filled' => 'Filled',
            'verified' => 'Verified',
            'approved' => 'Approved',
            'financed' => 'Financed',
            'total' => 'Total Expense'
        ];
        $cardData = [];
        foreach ($claimStatusCounts as $status) {
            $key = strtolower($status->ClaimStatus);
            if (isset($statusMap[$key])) {
                $cardData[] = ['label' => $statusMap[$key], 'value' => $status->TotalCount, 'originalValue' => $status->TotalCount];
            }
        }

        return response()->json([
            'cardData' => $cardData,
            'claimTypeTotals' => $claimTypeTotals,
            'departmentTotals' => $departmentTotals,
        ]);
    }
}
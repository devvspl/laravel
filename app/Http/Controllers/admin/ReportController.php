<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoreFunctions;
use App\Models\CoreVertical;
use App\Models\CoreDepartments;
use App\Models\HRMEmployees;
use App\Models\EligibilityPolicy;
use App\Models\ClaimType;
use App\Models\ExpenseClaim;
use App\Exports\ClaimReportExport;
use App\Exports\ClaimTypeWiseClaimReportExport;
use App\Exports\DepartmentWiseClaimReportExport;
use App\Exports\MonthWiseClaimReportExport;
use App\Exports\DailyActivityReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * This controller handles reports in the admin area, like expense claims and daily activity reports.
 */
class ReportController extends Controller
{
    /**
     * Shows the claim report page.
     *
     * Loads a page with data like active functions, verticals, departments, employees,
     * claim types, and policies for filtering expense claims.
     */
    public function claimReport()
    {
        try {
            $functions = CoreFunctions::where('is_active', 1)->get(['id', 'function_name']);
            $verticals = CoreVertical::where('is_active', 1)->get(['id', 'vertical_name']);
            $departments = CoreDepartments::where('is_active', 1)->get(['id', 'department_name']);
            $employees = HRMEmployees::select(
                'hrims.hrm_employee.EmployeeID',
                'hrims.hrm_employee.EmpCode',
                'hrims.hrm_employee.Fname',
                'hrims.hrm_employee.Sname',
                'hrims.hrm_employee.Lname',
                'hrims.hrm_employee.EmpStatus'
            )
                ->join('hrims.hrm_employee_general', 'hrims.hrm_employee.EmployeeID', '=', 'hrims.hrm_employee_general.EmployeeID')
                ->get();
            $claimTypes = ClaimType::where('ClaimStatus', 'A')->get(['ClaimId', 'ClaimName']);
            $eligibility_policy = EligibilityPolicy::where('CompanyId', session('company_id'))->get(['PolicyId', 'PolicyName']);
            return view('admin.claim_report', compact('functions', 'verticals', 'departments', 'employees', 'claimTypes', 'eligibility_policy'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error loading claim report: ' . $e->getMessage()]);
        }
    }

    /**
     * Shows the daily activity report page.
     *
     * Loads a page for viewing daily activity data, like uploads or approvals.
     */
    public function dailyActivity()
    {
        try {
            return view('admin.daily_activity');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error loading claim report: ' . $e->getMessage()]);
        }
    }

    /**
     * Gets a list of employees by department.
     *
     * Fetches employees for a specific company and department(s), returning their details
     * like ID, code, and name as a JSON response.
     */
    public function getEmployeesByDepartment(Request $request)
    {
        try {
            $departmentIds = $request->input('department_ids', []);

            $query = HRMEmployees::select(
                'hrims.hrm_employee.EmployeeID',
                'hrims.hrm_employee.EmpCode',
                'hrims.hrm_employee.Fname',
                'hrims.hrm_employee.Sname',
                'hrims.hrm_employee.Lname',
                'hrims.hrm_employee.EmpStatus'
            )
                ->join('hrims.hrm_employee_general', 'hrims.hrm_employee.EmployeeID', '=', 'hrims.hrm_employee_general.EmployeeID')
                ->where('hrims.hrm_employee.CompanyId', session('company_id'));

            if (!empty($departmentIds)) {
                $query->whereIn('hrims.hrm_employee_general.DepartmentId', $departmentIds);
            }

            $employees = $query->get();

            return $this->jsonSuccess($employees, 'Employees fetched successfully.');
        } catch (\Exception $e) {
            return $this->jsonError('Error fetching employees: ' . $e->getMessage());
        }
    }

    /**
     * Filters expense claims based on user input.
     *
     * Takes filters like department, date range, or claim type, grabs matching claims
     * from the database, and returns them as a paginated JSON response for a table.
     */
    public function filterClaims(Request $request)
    {
        try {
            $perPage = $request->input('length', 10);
            $start = $request->input('start', 0);
            $page = floor($start / $perPage) + 1;

            $filters = [
                'function_ids' => $request->input('function_ids', []),
                'vertical_ids' => $request->input('vertical_ids', []),
                'department_ids' => $request->input('department_ids', []),
                'user_ids' => $request->input('user_ids', []),
                'months' => $request->input('months', []),
                'claim_type_ids' => $request->input('claim_type_ids', []),
                'claim_statuses' => $request->input('claim_statuses', []),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
                'date_type' => $request->input('date_type', 'billDate'),
                'policy_ids' => $request->input('policy_ids', []),
                'vehicle_types' => $request->input('vehicle_types', []),
                'wheeler_type' => $request->input('wheeler_type')
            ];

            $table = ExpenseClaim::tableName(); 

            
            $query = \DB::table($table)
                ->select([
                    \DB::raw("DISTINCT {$table}.ExpId as ExpId"),
                    "{$table}.ClaimId as ClaimID",
                    'claimtype.ClaimName as ClaimType',
                    \DB::raw("CONCAT(hrims.hrm_employee.Fname, ' ', COALESCE(hrims.hrm_employee.Sname, ''), ' ', hrims.hrm_employee.Lname) as EmpName"),
                    'hrims.hrm_employee.EmpCode',
                    'hrims.core_functions.function_name as FunctionName',
                    'hrims.core_verticals.vertical_name as VerticalName',
                    'hrims.core_departments.department_name as DepartmentName',
                    'hrims.hrm_master_eligibility_policy.PolicyName as PolicyName',
                    'hrims.hrm_employee_eligibility.VehicleType as VehicleType',
                    "{$table}.ClaimMonth",
                    "{$table}.CrDate as UploadDate",
                    "{$table}.BillDate",
                    "{$table}.FilledTAmt as ClaimedAmount",
                    "{$table}.ClaimAtStep as ClaimStatus"
                ])
                ->leftJoin('claimtype', "{$table}.ClaimId", '=', 'claimtype.ClaimId')
                ->join('hrims.hrm_employee', "{$table}.FilledBy", '=', 'hrims.hrm_employee.EmployeeID')
                ->join('hrims.hrm_employee_general', 'hrims.hrm_employee.EmployeeID', '=', 'hrims.hrm_employee_general.EmployeeID')
                ->join('hrims.hrm_employee_eligibility', 'hrims.hrm_employee.EmployeeID', '=', 'hrims.hrm_employee_eligibility.EmployeeID')
                ->join('hrims.core_departments', 'hrims.hrm_employee_general.DepartmentId', '=', 'hrims.core_departments.id')
                ->leftJoin('hrims.core_functions', 'hrims.hrm_employee_general.EmpFunction', '=', 'hrims.core_functions.id')
                ->leftJoin('hrims.core_verticals', 'hrims.hrm_employee_general.EmpVertical', '=', 'hrims.core_verticals.id')
                ->leftJoin('hrims.hrm_master_eligibility_policy', 'hrims.hrm_employee_eligibility.VehiclePolicy', '=', 'hrims.hrm_master_eligibility_policy.PolicyId');

            
            if (!empty($filters['function_ids'])) {
                $query->whereIn('hrims.hrm_employee_general.EmpFunction', $filters['function_ids']);
            }
            if (!empty($filters['vertical_ids'])) {
                $query->whereIn('hrims.hrm_employee_general.EmpVertical', $filters['vertical_ids']);
            }
            if (!empty($filters['department_ids'])) {
                $query->whereIn('hrims.hrm_employee_general.DepartmentId', $filters['department_ids']);
            }
            if (!empty($filters['user_ids'])) {
                $query->whereIn('hrims.hrm_employee.EmpCode', $filters['user_ids']);
            }
            if (!empty($filters['months'])) {
                $query->whereIn("{$table}.ClaimMonth", $filters['months']);
            }
            if (!empty($filters['claim_type_ids'])) {
                if (in_array(7, $filters['claim_type_ids'])) {
                    $query->where("{$table}.ClaimId", 7);
                    if (!empty($filters['wheeler_type'])) {
                        $query->where("{$table}.WType", $filters['wheeler_type']);
                    }
                } else {
                    $query->whereIn("{$table}.ClaimId", $filters['claim_type_ids']);
                }
            }
            if (!empty($filters['policy_ids'])) {
                $query->whereIn('hrims.hrm_employee_eligibility.VehiclePolicy', $filters['policy_ids']);
            }
            if (!empty($filters['vehicle_types'])) {
                $query->whereIn('hrims.hrm_employee_eligibility.VehicleType', $filters['vehicle_types']);
            }
            if (!empty($filters['claim_statuses'])) {
                $query->whereIn("{$table}.ClaimAtStep", $filters['claim_statuses']);
            }
            if ($filters['from_date'] && $filters['to_date']) {
                $dateColumn = match ($filters['date_type']) {
                    'billDate' => 'BillDate',
                    'uploadDate' => 'CrDate',
                    'filledDate' => 'FilledDate',
                    default => 'BillDate',
                };
                $query->whereBetween("{$table}.{$dateColumn}", [$filters['from_date'], $filters['to_date']]);
            }

            $totalRecords = $query->count(\DB::raw("DISTINCT {$table}.ExpId"));

            $claims = $query->skip($start)->take($perPage)->get();

            foreach ($claims as $index => $claim) {
                $claim->Sn = $start + $index + 1;
            }

            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords,
                "data" => $claims,
            ]);
        } catch (\Exception $e) {
            \Log::error('Claim Filter Error: ' . $e->getMessage());
            return response()->json(['error' => 'Error filtering claims: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Exports filtered expense claims to an Excel file.
     *
     * Takes filters and columns from the request, creates an Excel file based on the report type
     * (like month-wise or department-wise), and downloads it with a timestamped name.
     */
    public function export(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $filters = [
            'function_ids' => $request->input('functionSelect', []),
            'vertical_ids' => $request->input('verticalSelect', []),
            'department_ids' => $request->input('departmentSelect', []),
            'user_ids' => $request->input('userSelect', []),
            'months' => $request->input('monthSelect', []),
            'claim_type_ids' => $request->input('claimTypeSelect', []),
            'claim_statuses' => $request->input('claimStatusSelect', []),
            'from_date' => $request->input('fromDate'),
            'to_date' => $request->input('toDate'),
            'date_type' => $request->input('dateType', 'billDate'),
            'policy_ids' => $request->input('policySelect', []),
            'vehicle_types' => $request->input('vehicleTypeSelect', []),
            'wheeler_type' => $request->input('wheelerTypeSelect'),
        ];

        $columns = $request->input('columns', []);
        $reportType = $request->input('reportType', 'general');
        $protectSheets = $request->input('protectSheets', false);
        $table = ExpenseClaim::tableName();
        if (empty($columns)) {
            return response()->json(['error' => 'No columns selected for export'], 400);
        }
        try {
            $export = match ($reportType) {
                'month_wise' => new MonthWiseClaimReportExport($filters, $columns, $protectSheets, $table),
                'department_wise' => new DepartmentWiseClaimReportExport($filters, $columns, $protectSheets, $table),
                'claim_type_wise' => new ClaimTypeWiseClaimReportExport($filters, $columns, $protectSheets, $table),
                default => new ClaimReportExport($filters, $columns, 'Claims', $protectSheets, $table),
            };

            return Excel::download($export, 'expense_claims_' . date('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Gets data for the daily activity report.
     *
     * Fetches counts of actions (like uploads, approvals) between two dates and
     * returns them as a JSON response for displaying in a report.
     */
    public function getDailyActivityData(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $query = "
            SELECT ActionDate,
                SUM(TotalUpload) AS TotalUpload,
                SUM(Punching) AS Punching,
                SUM(Verified) AS Verified,
                SUM(Approved) AS Approved,
                SUM(Financed) AS Financed
            FROM (
                SELECT DATE(CrDate) AS ActionDate, COUNT(*) AS TotalUpload, 0 AS Punching, 0 AS Verified, 0 AS Approved, 0 AS Financed
                FROM y7_expenseclaims
                WHERE CrDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(CrDate) BETWEEN ? AND ?
                GROUP BY DATE(CrDate)

                UNION ALL

                SELECT DATE(FilledDate), 0, COUNT(*), 0, 0, 0
                FROM y7_expenseclaims
                WHERE FilledDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(FilledDate) BETWEEN ? AND ?
                GROUP BY DATE(FilledDate)

                UNION ALL

                SELECT DATE(VerifyDate), 0, 0, COUNT(*), 0, 0
                FROM y7_expenseclaims
                WHERE VerifyDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(VerifyDate) BETWEEN ? AND ?
                GROUP BY DATE(VerifyDate)

                UNION ALL

                SELECT DATE(ApprDate), 0, 0, 0, COUNT(*), 0
                FROM y7_expenseclaims
                WHERE ApprDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(ApprDate) BETWEEN ? AND ?
                GROUP BY DATE(ApprDate)

                UNION ALL

                SELECT DATE(FinancedDate), 0, 0, 0, 0, COUNT(*)
                FROM y7_expenseclaims
                WHERE FinancedDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(FinancedDate) BETWEEN ? AND ?
                GROUP BY DATE(FinancedDate)
            ) AS progress
            GROUP BY ActionDate
            ORDER BY ActionDate
        ";

        $data = DB::select($query, [
            $fromDate,
            $toDate,
            $fromDate,
            $toDate,
            $fromDate,
            $toDate,
            $fromDate,
            $toDate,
            $fromDate,
            $toDate,
        ]);

        return response()->json([
            'data' => array_map(function ($row) {
                return [
                    'ActionDate' => $row->ActionDate,
                    'TotalUpload' => $row->TotalUpload,
                    'Punching' => $row->Punching,
                    'Verified' => $row->Verified,
                    'Approved' => $row->Approved,
                    'Financed' => $row->Financed,
                ];
            }, $data),
        ]);
    }

    /**
     * Exports daily activity data to an Excel file.
     *
     * Creates an Excel file with daily activity data (like uploads or approvals)
     * between two dates and downloads it with a descriptive name.
     */
    public function exportDailyActivity(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        return Excel::download(
            new DailyActivityReportExport($fromDate, $toDate),
            'daily_activity_report_' . $fromDate . '_to_' . $toDate . '.xlsx'
        );
    }
}
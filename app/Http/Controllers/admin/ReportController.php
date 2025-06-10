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
use Yajra\DataTables\Facades\DataTables;

/**
 * This controller manages reports for the admin section, like expense claims and daily activity reports.
 */
class ReportController extends Controller
{
    /**
     * Loads the expense claim report page.
     *
     * This method shows a page where admins can view and filter expense claims. It grabs lists of active functions,
     * verticals, departments, employees, claim types, and policies to help with filtering.
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

            return back()->withErrors(['error' => 'Could not load the claim report page: ' . $e->getMessage()]);
        }
    }

    /**
     * Loads the daily activity report page.
     *
     * This method shows a page where admins can see daily activity data, like how many claims were uploaded or approved.
     */
    public function dailyActivity()
    {
        try {

            return view('admin.daily_activity');
        } catch (\Exception $e) {

            return back()->withErrors(['error' => 'Could not load the daily activity page: ' . $e->getMessage()]);
        }
    }

    /**
     * Gets a list of employees for selected departments.
     *
     * This method finds employees based on the company and department(s) chosen, then returns their details
     * (like ID, code, and name) as a JSON response for use in dropdowns or tables.
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
            return $this->jsonSuccess($employees, 'Employees loaded successfully.');
        } catch (\Exception $e) {

            return $this->jsonError('Could not load employees: ' . $e->getMessage());
        }
    }

    /**
     * Builds a query for expense claims with filters.
     *
     * This helper method creates a database query to fetch expense claims, joining tables like employees,
     * departments, and claim types. It applies filters (like department or date range) to narrow down the results.
     */
    private function buildClaimQuery(array $filters, string $table)
    {

        $query = DB::table("{$table}")
            ->select([
                "{$table}.ExpId as ExpId",
                DB::raw("MAX({$table}.ClaimId) as ClaimId"),
                DB::raw("MAX(claimtype.ClaimName) as claim_type_name"),
                DB::raw("MAX(CONCAT(hrims.hrm_employee.Fname, ' ', COALESCE(hrims.hrm_employee.Sname, ''), ' ', hrims.hrm_employee.Lname)) as employee_name"),
                DB::raw("MAX(hrims.hrm_employee.EmpCode) as employee_code"),
                DB::raw("MAX(hrims.core_functions.function_name) as function_name"),
                DB::raw("MAX(hrims.core_verticals.vertical_name) as vertical_name"),
                DB::raw("MAX(hrims.core_departments.department_name) as department_name"),
                DB::raw("MAX(hrims.hrm_master_eligibility_policy.PolicyName) as policy_name"),
                DB::raw("MAX(hrims.hrm_employee_eligibility.VehicleType) as vehicle_type"),
                DB::raw("MAX({$table}.ClaimMonth) as ClaimMonth"),
                DB::raw("MAX({$table}.CrDate) as CrDate"),
                DB::raw("MAX({$table}.BillDate) as BillDate"),
                DB::raw("MAX({$table}.FilledTAmt) as FilledTAmt"),
                DB::raw("MAX({$table}.ClaimAtStep) as ClaimAtStep"),
                DB::raw("MAX({$table}.FilledDate) as FilledDate"),
                DB::raw("MAX({$table}.odomtr_opening) as odomtr_opening"),
                DB::raw("MAX({$table}.odomtr_closing) as odomtr_closing"),
                DB::raw("MAX({$table}.TotKm) as TotKm"),
                DB::raw("MAX({$table}.WType) as WType"),
                DB::raw("MAX({$table}.RatePerKM) as RatePerKM"),
                DB::raw("MAX({$table}.VerifyTAmt) as VerifyTAmt"),
                DB::raw("MAX({$table}.VerifyTRemark) as VerifyTRemark"),
                DB::raw("MAX({$table}.VerifyDate) as VerifyDate"),
                DB::raw("MAX({$table}.ApprTAmt) as ApprTAmt"),
                DB::raw("MAX({$table}.ApprTRemark) as ApprTRemark"),
                DB::raw("MAX({$table}.ApprDate) as ApprDate"),
                DB::raw("MAX({$table}.FinancedTAmt) as FinancedTAmt"),
                DB::raw("MAX({$table}.FinancedTRemark) as FinancedTRemark"),
                DB::raw("MAX({$table}.FinancedDate) as FinancedDate"),
            ])
            ->leftJoin('claimtype', "{$table}.ClaimId", '=', 'claimtype.ClaimId')
            ->leftJoin('hrims.hrm_employee', "{$table}.CrBy", '=', 'hrims.hrm_employee.EmployeeID')
            ->leftJoin('hrims.hrm_employee_general', 'hrims.hrm_employee.EmployeeID', '=', 'hrims.hrm_employee_general.EmployeeID')
            ->leftJoin('hrims.hrm_employee_eligibility', 'hrims.hrm_employee.EmployeeID', '=', 'hrims.hrm_employee_eligibility.EmployeeID')
            ->leftJoin('hrims.core_departments', 'hrims.hrm_employee_general.DepartmentId', '=', 'hrims.core_departments.id')
            ->leftJoin('hrims.core_functions', 'hrims.hrm_employee_general.EmpFunction', '=', 'hrims.core_functions.id')
            ->leftJoin('hrims.core_verticals', 'hrims.hrm_employee_general.EmpVertical', '=', 'hrims.core_verticals.id')
            ->leftJoin('hrims.hrm_master_eligibility_policy', 'hrims.hrm_employee_eligibility.VehiclePolicy', '=', 'hrims.hrm_master_eligibility_policy.PolicyId')
            ->groupBy("{$table}.ExpId")
            ->orderBy("{$table}.ExpId", 'asc');
            
        $query = $this->applyClaimFilters($query, $filters, $table);
        return $query;
    }

    /**
     * Filters expense claims based on user selections.
     *
     * This method takes filters like department, date range, or claim type, finds matching claims
     * in the database, and returns them as a JSON response for a table, with pagination.
     */
    public function filterClaims(Request $request)
    {
        try {
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
                'wheeler_type' => $request->input('wheeler_type'),
            ];

            $table = ExpenseClaim::tableName();
            $query = $this->buildClaimQuery($filters, $table); 

            return DataTables::of($query)
                ->addIndexColumn() 
                ->editColumn('ClaimAtStep', function ($row) {
                    $badgeClass = 'bg-secondary-subtle text-secondary';
                    $statusText = 'Unknown';

                    switch ($row->ClaimAtStep) {
                        case 1:
                            $badgeClass = 'bg-dark-subtle text-dark';
                            $statusText = 'Deactivate';
                            break;
                        case 2:
                            $badgeClass = 'bg-warning-subtle text-warning';
                            $statusText = 'Draft / Submitted';
                            break;
                        case 3:
                            $badgeClass = 'bg-info-subtle text-info';
                            $statusText = 'Filled';
                            break;
                        case 4:
                            $badgeClass = 'bg-primary-subtle text-primary';
                            $statusText = 'Verified';
                            break;
                        case 5:
                            $badgeClass = 'bg-success-subtle text-success';
                            $statusText = 'Approved';
                            break;
                        case 6:
                            $badgeClass = 'bg-success-subtle text-success';
                            $statusText = 'Financed';
                            break;
                    }

                    return '<span class="badge ' . $badgeClass . ' badge-border">' . $statusText . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#claimDetailModal" id="viewClaimDetail"><i class="ri-eye-fill"></i></button>';
                })
                ->rawColumns(['ClaimAtStep', 'action']) 
                ->make(true);

        } catch (\Exception $e) {
            \Log::error('Claim Filter Error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load claims: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exports expense claims to an Excel file.
     *
     * This method takes filters and selected columns, creates an Excel file with the claims data,
     * and downloads it. It supports different report types like monthly or department-wise reports.
     */
    public function export(Request $request)
    {

        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');


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
            'wheeler_type' => $request->input('wheeler_type'),
        ];


        $columns = $request->input('columns', []);
        $reportType = $request->input('reportType', 'general');
        $protectSheets = $request->input('protectSheets', false);
        $table = ExpenseClaim::tableName();


        if (empty($columns)) {
            return response()->json(['error' => 'Please select at least one column to export'], 400);
        }

        try {

            $query = $this->buildClaimQuery($filters, $table);

            $export = match ($reportType) {
                'month_wise' => new MonthWiseClaimReportExport($query, $filters, $columns, $protectSheets, $table),
                'department_wise' => new DepartmentWiseClaimReportExport($query, $filters, $columns, $protectSheets, $table),
                'claim_type_wise' => new ClaimTypeWiseClaimReportExport($query, $filters, $columns, $protectSheets, $table),
                default => new ClaimReportExport($query, $filters, $columns, 'Claims', $protectSheets, $table),
            };


            return Excel::download($export, 'expense_claims_' . date('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {

            \Log::error('Export Error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not export claims: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Applies filters to the expense claim query.
     *
     * This helper method adds filters (like department, date, or claim type) to the database query
     * to show only the claims that match the user's selections.
     */
    private function applyClaimFilters($query, $filters, $table)
    {

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

        return $query;
    }

    /**
     * Gets data for the daily activity report.
     *
     * This method counts actions (like uploads, approvals, or payments) between two dates
     * and returns the counts as a JSON response to show in a report.
     */
    public function getDailyActivityData(Request $request)
    {
        try {

            $fromDate = $request->input('fromDate');
            $toDate = $request->input('toDate');


            $table = ExpenseClaim::tableName();


            $baseQuery = DB::table("{$table} as e")
                ->where('e.ClaimStatus', '!=', 'Deactivate')
                ->whereNotIn('e.ClaimId', [19, 20, 21]);


            $uploadQuery = (clone $baseQuery)
                ->selectRaw('DATE(e.CrDate) AS ActionDate, COUNT(*) AS TotalUpload, 0 AS Punching, 0 AS Verified, 0 AS Approved, 0 AS Financed')
                ->where('e.CrDate', '!=', '0000-00-00')
                ->whereBetween(DB::raw('DATE(e.CrDate)'), [$fromDate, $toDate])
                ->groupBy(DB::raw('DATE(e.CrDate)'));


            $punchingQuery = (clone $baseQuery)
                ->selectRaw('DATE(e.FilledDate) AS ActionDate, 0 AS TotalUpload, COUNT(*) AS Punching, 0 AS Verified, 0 AS Approved, 0 AS Financed')
                ->where('e.FilledDate', '!=', '0000-00-00')
                ->whereBetween(DB::raw('DATE(e.FilledDate)'), [$fromDate, $toDate])
                ->groupBy(DB::raw('DATE(e.FilledDate)'));


            $verifiedQuery = (clone $baseQuery)
                ->selectRaw('DATE(e.VerifyDate) AS ActionDate, 0 AS TotalUpload, 0 AS Punching, COUNT(*) AS Verified, 0 AS Approved, 0 AS Financed')
                ->where('e.VerifyDate', '!=', '0000-00-00')
                ->whereBetween(DB::raw('DATE(e.VerifyDate)'), [$fromDate, $toDate])
                ->groupBy(DB::raw('DATE(e.VerifyDate)'));


            $approvedQuery = (clone $baseQuery)
                ->selectRaw('DATE(e.ApprDate) AS ActionDate, 0 AS TotalUpload, 0 AS Punching, 0 AS Verified, COUNT(*) AS Approved, 0 AS Financed')
                ->where('e.ApprDate', '!=', '0000-00-00')
                ->whereBetween(DB::raw('DATE(e.ApprDate)'), [$fromDate, $toDate])
                ->groupBy(DB::raw('DATE(e.ApprDate)'));


            $financedQuery = (clone $baseQuery)
                ->selectRaw('DATE(e.FinancedDate) AS ActionDate, 0 AS TotalUpload, 0 AS Punching, 0 AS Verified, 0 AS Approved, COUNT(*) AS Financed')
                ->where('e.FinancedDate', '!=', '0000-00-00')
                ->whereBetween(DB::raw('DATE(e.FinancedDate)'), [$fromDate, $toDate])
                ->groupBy(DB::raw('DATE(e.FinancedDate)'));


            $data = $uploadQuery
                ->unionAll($punchingQuery)
                ->unionAll($verifiedQuery)
                ->unionAll($approvedQuery)
                ->unionAll($financedQuery)
                ->newQuery()
                ->selectRaw('ActionDate, SUM(TotalUpload) AS TotalUpload, SUM(Punching) AS Punching, SUM(Verified) AS Verified, SUM(Approved) AS Approved, SUM(Financed) AS Financed')
                ->groupBy('ActionDate')
                ->orderBy('ActionDate')
                ->get();


            $formattedData = $data->map(function ($row) {
                return [
                    'ActionDate' => $row->ActionDate,
                    'TotalUpload' => $row->TotalUpload,
                    'Punching' => $row->Punching,
                    'Verified' => $row->Verified,
                    'Approved' => $row->Approved,
                    'Financed' => $row->Financed,
                ];
            })->toArray();


            return $this->jsonSuccess($formattedData, 'Daily activity data loaded successfully.');
        } catch (\Exception $e) {

            \Log::error('Daily Activity Data Error: ' . $e->getMessage());
            return $this->jsonError('Could not load daily activity data: ' . $e->getMessage());
        }
    }

    /**
     * Exports daily activity data to an Excel file.
     *
     * This method creates an Excel file with daily activity data (like uploads or approvals)
     * between two dates and downloads it with a clear file name.
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
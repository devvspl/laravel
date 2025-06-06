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
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the claim report view with filter options.
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
     * Fetch active functions (kept for potential other uses).
     */
    public function getFunction()
    {
        try {
            $functions = CoreFunctions::where('is_active', 1)->get();
            return $this->jsonSuccess($functions, 'Functions fetched successfully.');
        } catch (\Exception $e) {
            return $this->jsonError('Error fetching functions: ' . $e->getMessage());
        }
    }

    /**
     * Fetch active verticals.
     */
    public function getVertical()
    {
        try {
            $verticals = CoreVertical::where('is_active', 1)->get();
            return $this->jsonSuccess($verticals, 'Verticals fetched successfully.');
        } catch (\Exception $e) {
            return $this->jsonError('Error fetching verticals: ' . $e->getMessage());
        }
    }

    /**
     * Fetch active departments.
     */
    public function getDepartment()
    {
        try {
            $departments = CoreDepartments::where('is_active', 1)->get();
            return $this->jsonSuccess($departments, 'Departments fetched successfully.');
        } catch (\Exception $e) {
            return $this->jsonError('Error fetching departments: ' . $e->getMessage());
        }
    }

    /**
     * Fetch employees by department IDs.
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
     * Fetch active claim types.
     */
    public function getClaimTypes()
    {
        try {
            $claimTypes = ClaimType::where('ClaimStatus', 'A')->get();
            return $this->jsonSuccess($claimTypes, 'Claim types fetched successfully.');
        } catch (\Exception $e) {
            return $this->jsonError('Error fetching claim types: ' . $e->getMessage());
        }
    }

    /**
     * Filter claims based on search parameters.
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

            $query = ExpenseClaim::select([
                DB::raw('DISTINCT y7_expenseclaims.ExpId as ExpId'),
                'y7_expenseclaims.ClaimId as ClaimID',
                'claimtype.ClaimName as ClaimType',
                DB::raw("CONCAT(hrims.hrm_employee.Fname, ' ', COALESCE(hrims.hrm_employee.Sname, ''), ' ', hrims.hrm_employee.Lname) as EmpName"),
                'hrims.hrm_employee.EmpCode',
                'hrims.core_functions.function_name as FunctionName',
                'hrims.core_verticals.vertical_name as VerticalName',
                'hrims.core_departments.department_name as DepartmentName',
                'hrims.hrm_master_eligibility_policy.PolicyName as PolicyName',
                'hrims.hrm_employee_eligibility.VehicleType as VehicleType',
                'y7_expenseclaims.ClaimMonth',
                'y7_expenseclaims.CrDate as UploadDate',
                'y7_expenseclaims.BillDate',
                'y7_expenseclaims.FilledTAmt as ClaimedAmount',
                'y7_expenseclaims.ClaimAtStep as ClaimStatus'
            ])
                ->leftJoin('claimtype', 'y7_expenseclaims.ClaimId', '=', 'claimtype.ClaimId')
                ->join('hrims.hrm_employee', 'y7_expenseclaims.FilledBy', '=', 'hrims.hrm_employee.EmployeeID')
                ->join('hrims.hrm_employee_general', 'hrims.hrm_employee.EmployeeID', '=', 'hrims.hrm_employee_general.EmployeeID')
                ->join('hrims.hrm_employee_eligibility', 'hrims.hrm_employee.EmployeeID', '=', 'hrims.hrm_employee_eligibility.EmployeeID')
                ->join('hrims.core_departments', 'hrims.hrm_employee_general.DepartmentId', '=', 'hrims.core_departments.id')
                ->leftJoin('hrims.core_functions', 'hrims.hrm_employee_general.EmpFunction', '=', 'hrims.core_functions.id')
                ->leftJoin('hrims.core_verticals', 'hrims.hrm_employee_general.EmpVertical', '=', 'hrims.core_verticals.id')
                ->leftJoin('hrims.hrm_master_eligibility_policy', 'hrims.hrm_employee_eligibility.VehiclePolicy', '=', 'hrims.hrm_master_eligibility_policy.PolicyId');

            // Apply Filters
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
                $query->whereIn('y7_expenseclaims.ClaimMonth', $filters['months']);
            }
            if (!empty($filters['claim_type_ids'])) {
                if (in_array(7, $filters['claim_type_ids'])) {
                    $query->where('y7_expenseclaims.ClaimId', 7);
                    if (!empty($filters['wheeler_type'])) {
                        $query->where('y7_expenseclaims.WType', $filters['wheeler_type']);
                    }
                } else {
                    $query->whereIn('y7_expenseclaims.ClaimId', $filters['claim_type_ids']);
                }
            }
            if (!empty($filters['policy_ids'])) {
                $query->whereIn('hrims.hrm_employee_eligibility.VehiclePolicy', $filters['policy_ids']);
            }
            if (!empty($filters['vehicle_types'])) {
                $query->whereIn('hrims.hrm_employee_eligibility.VehicleType', $filters['vehicle_types']);
            }
            if (!empty($filters['claim_statuses'])) {
                $query->whereIn('y7_expenseclaims.ClaimAtStep', $filters['claim_statuses']);
            }
            if ($filters['from_date'] && $filters['to_date']) {
                $dateColumn = match ($filters['date_type']) {
                    'billDate' => 'BillDate',
                    'uploadDate' => 'CrDate',
                    'filledDate' => 'FilledDate',
                    default => 'BillDate',
                };
                $query->whereBetween('y7_expenseclaims.' . $dateColumn, [$filters['from_date'], $filters['to_date']]);
            }

            $totalRecords = $query->count(DB::raw('DISTINCT y7_expenseclaims.ExpId'));

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
            Log::error('Claim Filter Error: ' . $e->getMessage());
            return response()->json(['error' => 'Error filtering claims: ' . $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        // Increase execution time
        ini_set('max_execution_time', 300); // 5 minutes
        ini_set('memory_limit', '512M'); // Increase memory limit

        $filters = [
            'functionSelect' => $request->input('functionSelect', []),
            'verticalSelect' => $request->input('verticalSelect', []),
            'departmentSelect' => $request->input('departmentSelect', []),
            'userSelect' => $request->input('userSelect', []),
            'monthSelect' => $request->input('monthSelect', []),
            'claimTypeSelect' => $request->input('claimTypeSelect', []),
            'claimStatusSelect' => $request->input('claimStatusSelect', []),
            'fromDate' => $request->input('fromDate'),
            'toDate' => $request->input('toDate'),
            'dateType' => $request->input('dateType', 'billDate'),
            'policySelect' => $request->input('policySelect', []),
            'vehicleTypeSelect' => $request->input('vehicleTypeSelect', []),
            'wheelerTypeSelect' => $request->input('wheelerTypeSelect', []),
        ];

        $columns = $request->input('columns', []);

        if (empty($columns)) {
            return response()->json(['error' => 'No columns selected for export'], 400);
        }

        // Log query for debugging
        DB::enableQueryLog();
        try {
            $export = new ClaimReportExport($filters, $columns);
            $query = $export->query();
            $start = microtime(true);
            $query->count(); // Test query performance
            $duration = microtime(true) - $start;
            Log::info('Export query executed', [
                'duration' => $duration,
                'query' => DB::getQueryLog(),
                'filters' => $filters,
                'columns' => $columns,
            ]);

            return Excel::download($export, 'expense_claims_' . date('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Export failed', [
                'error' => $e->getMessage(),
                'query' => DB::getQueryLog(),
                'filters' => $filters,
                'columns' => $columns,
            ]);
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

}
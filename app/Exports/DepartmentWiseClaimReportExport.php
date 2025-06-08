<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Exports\ClaimReportExport;

class DepartmentWiseClaimReportExport implements WithMultipleSheets
{
    protected $filters;
    protected $columns;
    protected $protectSheets;
    protected $table;

    public function __construct(array $filters, array $columns, bool $protectSheets = false, $table)
    {
        $this->filters = $filters;
        $this->columns = $columns;
        $this->protectSheets = $protectSheets;
        $this->table = $table;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Get distinct departments in the data
        $query = DB::table("{$this->table} as e")
            ->join('hrims.hrm_employee as emp', 'e.FilledBy', '=', 'emp.EmployeeID')
            ->join('hrims.hrm_employee_general as eg', 'emp.EmployeeID', '=', 'eg.EmployeeID')
            ->join('hrims.core_departments as d', 'eg.DepartmentId', '=', 'd.id');

        // Apply date filter if provided
        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $dateColumn = match ($this->filters['date_type']) {
                'billDate' => 'e.BillDate',
                'uploadDate' => 'e.CrDate',
                'filledDate' => 'e.FilledDate',
                default => 'e.BillDate',
            };
            $query->whereBetween($dateColumn, [$this->filters['from_date'], $this->filters['to_date']]);
        }

        $departments = $query->selectRaw('DISTINCT eg.DepartmentId, d.department_name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->DepartmentId => $item->department_name];
            })
            ->toArray();

        // If no departments found, return a single "No Data" sheet
        if (empty($departments)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
            return $sheets;
        }

        // Check each department for data and create sheets only for departments with data
        foreach ($departments as $departmentId => $departmentName) {
            $deptFilters = array_merge($this->filters, ['department_ids' => [$departmentId]]);
            $tempExport = new ClaimReportExport($deptFilters, $this->columns, $departmentName, $this->protectSheets, $this->table);
            $count = $tempExport->query()->count(DB::raw('DISTINCT e.ExpId'));

            if ($count > 0) {
                $sheets[] = new ClaimReportExport($deptFilters, $this->columns, $departmentName, $this->protectSheets, $this->table);
            }
        }

        // If no sheets were created (no data for any department), add a "No Data" sheet
        if (empty($sheets)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
        }

        return $sheets;
    }
}
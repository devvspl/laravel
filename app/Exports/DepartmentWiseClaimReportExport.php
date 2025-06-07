<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Exports\ClaimReportExport;

class DepartmentWiseClaimReportExport implements WithMultipleSheets
{
    protected $filters;
    protected $columns;

    public function __construct(array $filters, array $columns)
    {
        $this->filters = $filters;
        $this->columns = $columns;
    }

    public function sheets(): array
    {
        $sheets = [];

        
        $query = DB::table('y7_expenseclaims as e')
            ->join('hrims.hrm_employee as emp', 'e.FilledBy', '=', 'emp.EmployeeID')
            ->join('hrims.hrm_employee_general as eg', 'emp.EmployeeID', '=', 'eg.EmployeeID')
            ->join('hrims.core_departments as d', 'eg.DepartmentId', '=', 'd.id');

        
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

        
        if (empty($departments)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data');
            return $sheets;
        }

        
        foreach ($departments as $departmentId => $departmentName) {
            $deptFilters = array_merge($this->filters, ['department_ids' => [$departmentId]]);
            $tempExport = new ClaimReportExport($deptFilters, $this->columns);
            $count = $tempExport->query()->count(DB::raw('DISTINCT e.ExpId'));

            if ($count > 0) {
                $sheets[] = new ClaimReportExport($deptFilters, $this->columns, $departmentName);
            }
        }

        
        if (empty($sheets)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data');
        }

        return $sheets;
    }
}
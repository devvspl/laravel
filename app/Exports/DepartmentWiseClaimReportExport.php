<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Exports\ClaimReportExport;
use Illuminate\Support\Collection;

class DepartmentWiseClaimReportExport implements WithMultipleSheets
{
    protected $query;
    protected $filters;
    protected $columns;
    protected $protectSheets;
    protected $table;

    public function __construct($query, array $filters, array $columns, bool $protectSheets = false, $table)
    {
        $this->query = $query;
        $this->filters = $filters;
        $this->columns = $columns;
        $this->protectSheets = $protectSheets;
        $this->table = $table;
    }

    public function sheets(): array
    {
        $sheets = [];


        $departments = (clone $this->query)
            ->selectRaw('DISTINCT hrims.core_departments.id AS DepartmentId, hrims.core_departments.department_name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->DepartmentId => $item->department_name];
            })
            ->toArray();


        if (empty($departments)) {
            $sheets[] = new ClaimReportExport($this->query, $this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
            return $sheets;
        }


        foreach ($departments as $departmentId => $departmentName) {

            $deptQuery = (clone $this->query)->where('hrims.hrm_employee_general.DepartmentId', $departmentId);


            $count = $deptQuery->count(DB::raw('DISTINCT ExpenseClaim.ExpId'));

            if ($count > 0) {

                $deptFilters = array_merge($this->filters, ['department_ids' => [$departmentId]]);
                $sheets[] = new ClaimReportExport(
                    $deptQuery,
                    $deptFilters,
                    $this->columns,
                    $departmentName ?? 'Department ' . $departmentId,
                    $this->protectSheets,
                    $this->table
                );
            }
        }


        if (empty($sheets)) {
            $sheets[] = new ClaimReportExport($this->query, $this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
        }

        return $sheets;
    }
}
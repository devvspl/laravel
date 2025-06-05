<?php

namespace App\Exports;

use App\Models\ExpenseClaim;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClaimReportExport implements FromQuery, WithHeadings, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = ExpenseClaim::query();

        if (!empty($this->filters['functions'])) {
            $query->whereIn('function_id', $this->filters['functions']);
        }
        if (!empty($this->filters['verticals'])) {
            $query->whereIn('vertical_id', $this->filters['verticals']);
        }
        if (!empty($this->filters['departments'])) {
            $query->whereIn('department_id', $this->filters['departments']);
        }
        if (!empty($this->filters['users'])) {
            $query->whereIn('emp_code', $this->filters['users']);
        }
        if (!empty($this->filters['months'])) {
            $query->whereIn('month', $this->filters['months']);
        }
        if (!empty($this->filters['claimTypes'])) {
            $query->whereIn('claim_type_id', $this->filters['claimTypes']);
        }
        if (!empty($this->filters['claimStatuses'])) {
            $query->whereIn('claim_status', $this->filters['claimStatuses']);
        }
        if (!empty($this->filters['policies'])) {
            $query->whereIn('policy_id', $this->filters['policies']);
        }
        if (!empty($this->filters['vehicleTypes'])) {
            $query->whereIn('vehicle_type_id', $this->filters['vehicleTypes']);
        }
        if (!empty($this->filters['wheelerTypes'])) {
            $query->whereIn('wheeler_type', $this->filters['wheelerTypes']);
        }
        if (!empty($this->filters['fromDate']) && !empty($this->filters['toDate'])) {
            $dateType = $this->filters['dateType'] ?? 'billDate';
            if ($dateType === 'billDate') {
                $query->whereBetween('bill_date', [$this->filters['fromDate'], $this->filters['toDate']]);
            } elseif ($dateType === 'uploadDate') {
                $query->whereBetween('upload_date', [$this->filters['fromDate'], $this->filters['toDate']]);
            } elseif ($dateType === 'filledDate') {
                $query->whereBetween('filled_date', [$this->filters['fromDate'], $this->filters['toDate']]);
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Sn',
            'Claim ID',
            'Claim Type',
            'Emp Name',
            'Emp Code',
            'Month',
            'Upload Date',
            'Bill Date',
            'Claimed Amt',
            'Claim Status',
        ];
    }
}
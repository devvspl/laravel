<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Exports\ClaimReportExport;

class ClaimTypeWiseClaimReportExport implements WithMultipleSheets
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
            ->leftJoin('claimtype as ct', 'e.ClaimId', '=', 'ct.ClaimId');

        
        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $dateColumn = match ($this->filters['date_type']) {
                'billDate' => 'e.BillDate',
                'uploadDate' => 'e.CrDate',
                'filledDate' => 'e.FilledDate',
                default => 'e.BillDate',
            };
            $query->whereBetween($dateColumn, [$this->filters['from_date'], $this->filters['to_date']]);
        }

        $claimTypes = $query->selectRaw('DISTINCT e.ClaimId, ct.ClaimName')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->ClaimId => $item->ClaimName];
            })
            ->toArray();

        
        if (empty($claimTypes)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data');
            return $sheets;
        }

        
        foreach ($claimTypes as $claimId => $claimName) {
            $claimFilters = array_merge($this->filters, ['claim_type_ids' => [$claimId]]);
            $tempExport = new ClaimReportExport($claimFilters, $this->columns);
            $count = $tempExport->query()->count(DB::raw('DISTINCT e.ExpId'));

            if ($count > 0) {
                $sheets[] = new ClaimReportExport($claimFilters, $this->columns, $claimName ?? 'Claim Type ' . $claimId);
            }
        }

        
        if (empty($sheets)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data');
        }

        return $sheets;
    }
}
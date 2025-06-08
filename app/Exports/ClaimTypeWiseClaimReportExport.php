<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Exports\ClaimReportExport;

class ClaimTypeWiseClaimReportExport implements WithMultipleSheets
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

        // Get distinct claim types in the data
        $query = DB::table("{$this->table} as e")
            ->leftJoin('claimtype as ct', 'e.ClaimId', '=', 'ct.ClaimId');

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

        $claimTypes = $query->selectRaw('DISTINCT e.ClaimId, ct.ClaimName')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->ClaimId => $item->ClaimName];
            })
            ->toArray();

        // If no claim types found, return a single "No Data" sheet
        if (empty($claimTypes)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
            return $sheets;
        }

        // Check each claim type for data and create sheets only for claim types with data
        foreach ($claimTypes as $claimId => $claimName) {
            $claimFilters = array_merge($this->filters, ['claim_type_ids' => [$claimId]]);
            $tempExport = new ClaimReportExport($claimFilters, $this->columns, $claimName ?? 'Claim Type ' . $claimId, $this->protectSheets, $this->table);
            $count = $tempExport->query()->count(DB::raw('DISTINCT e.ExpId'));

            if ($count > 0) {
                $sheets[] = new ClaimReportExport($claimFilters, $this->columns, $claimName ?? 'Claim Type ' . $claimId, $this->protectSheets, $this->table);
            }
        }

        // If no sheets were created (no data for any claim type), add a "No Data" sheet
        if (empty($sheets)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
        }

        return $sheets;
    }
}
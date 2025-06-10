<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Exports\ClaimReportExport;
use Illuminate\Support\Collection;

class ClaimTypeWiseClaimReportExport implements WithMultipleSheets
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


        $claimTypes = (clone $this->query)
            ->selectRaw('DISTINCT claimtype.ClaimId, claimtype.ClaimName')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->ClaimId => $item->ClaimName];
            })
            ->toArray();


        if (empty($claimTypes)) {
            $sheets[] = new ClaimReportExport($this->query, $this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
            return $sheets;
        }


        foreach ($claimTypes as $claimId => $claimName) {

            $claimQuery = (clone $this->query)->where('claimtype.ClaimId', $claimId);


            $count = $claimQuery->count(DB::raw('DISTINCT ExpenseClaim.ExpId'));

            if ($count > 0) {

                $claimFilters = array_merge($this->filters, ['claim_type_ids' => [$claimId]]);
                $sheets[] = new ClaimReportExport(
                    $claimQuery,
                    $claimFilters,
                    $this->columns,
                    $claimName ?? 'Claim Type ' . $claimId,
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
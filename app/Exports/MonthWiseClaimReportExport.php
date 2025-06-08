<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Exports\ClaimReportExport;

class MonthWiseClaimReportExport implements WithMultipleSheets
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
        $monthMap = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        // Get distinct months in the selected date range
        $query = DB::table("{$this->table} as e");
        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $dateColumn = match ($this->filters['date_type']) {
                'billDate' => 'e.BillDate',
                'uploadDate' => 'e.CrDate',
                'filledDate' => 'e.FilledDate',
                default => 'e.BillDate',
            };
            $query->whereBetween($dateColumn, [$this->filters['from_date'], $this->filters['to_date']]);
        }
        $months = $query->selectRaw('DISTINCT e.ClaimMonth')
            ->pluck('ClaimMonth')
            ->toArray();

        // If no months found, return a single "No Data" sheet
        if (empty($months)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
            return $sheets;
        }

        // Check each month for data and create sheets only for months with data
        foreach ($months as $month) {
            $monthFilters = array_merge($this->filters, ['months' => [$month]]);
            $sheetName = $monthMap[$month] ?? 'Month ' . $month; // Define sheetName here
            $tempExport = new ClaimReportExport($monthFilters, $this->columns, $sheetName, $this->protectSheets, $this->table);
            $count = $tempExport->query()->count(DB::raw('DISTINCT e.ExpId'));

            if ($count > 0) {
                $sheets[] = new ClaimReportExport($monthFilters, $this->columns, $sheetName, $this->protectSheets, $this->table);
            }
        }

        // If no sheets were created (no data for any month), add a "No Data" sheet
        if (empty($sheets)) {
            $sheets[] = new ClaimReportExport($this->filters, $this->columns, 'No Data', $this->protectSheets, $this->table);
        }

        return $sheets;
    }
}
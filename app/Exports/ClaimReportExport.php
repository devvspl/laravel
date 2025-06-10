<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ClaimReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents, WithTitle, WithChunkReading
{
    protected $filters;
    protected $columns;
    protected $totals;
    protected $sheetName;
    protected $protectSheets;
    protected $table;
    protected $query;
    protected $rowNumber;

    public function __construct($query, array $filters, array $columns, string $sheetName = 'Claims', bool $protectSheets = false, $table)
    {
        $this->query = $query;
        $this->filters = $filters;
        $this->columns = $columns;
        $this->totals = [];
        $this->sheetName = $sheetName;
        $this->protectSheets = $protectSheets;
        $this->table = $table;
        $this->rowNumber = 0;
    }

    public function query()
    {
        return $this->query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function headings(): array
    {
        $headingsMap = [
            'sn' => 'Serial Number',
            'claim_id' => 'Claim ID',
            'claim_type' => 'Claim Type',
            'claim_status' => 'Claim Status',
            'emp_name' => 'Employee Name',
            'emp_code' => 'Employee Code',
            'function' => 'Function',
            'vertical' => 'Vertical',
            'department' => 'Department',
            'policy' => 'Policy',
            'vehicle_type' => 'Vehicle Type',
            'month' => 'Month',
            'upload_date' => 'Upload Date',
            'bill_date' => 'Bill Date',
            'claimed_amt' => 'Claimed Amount',
            'FilledAmt' => 'Filled Amount',
            'FilledDate' => 'Filled Date',
            'odomtr_opening' => 'Odometer Opening',
            'odomtr_closing' => 'Odometer Closing',
            'TotKm' => 'Total KM',
            'WType' => 'Wheeler Type',
            'RatePerKM' => 'Rate Per KM',
            'VerifyAmt' => 'Verified Amount',
            'VerifyTRemark' => 'Verify Remark',
            'VerifyDate' => 'Verify Date',
            'ApprAmt' => 'Approved Amount',
            'ApprTRemark' => 'Approval Remark',
            'ApprDate' => 'Approval Date',
            'FinancedAmt' => 'Financed Amount',
            'FinancedTRemark' => 'Finance Remark',
            'FinancedDate' => 'Finance Date',
        ];

        return array_map(fn($column) => $headingsMap[$column] ?? $column, $this->columns);
    }

    public function map($row): array
    {
        static $monthMap = [
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
        static $wheelerMap = [2 => '2 Wheeler', 4 => '4 Wheeler'];
        static $statusMap = [
        1 => 'Draft',
        2 => 'Submitted',
        3 => 'Filled',
        4 => 'Approved',
        5 => 'Financed',
        6 => 'Payment',
        ];

        $this->rowNumber++;

        $data = [];
        foreach ($this->columns as $index => $column) {
            $value = match ($column) {
                'sn' => $this->rowNumber,
                'claim_id' => $row->ClaimId ?? 'N/A',
                'claim_type' => $row->claim_type_name ?? 'N/A',
                'claim_status' => $statusMap[$row->ClaimAtStep] ?? 'N/A',
                'emp_name' => $row->employee_name ?? 'N/A',
                'emp_code' => $row->employee_code ?? 'N/A',
                'function' => $row->function_name ?? 'N/A',
                'vertical' => $row->vertical_name ?? 'N/A',
                'department' => $row->department_name ?? 'N/A',
                'policy' => $row->policy_name ?? '',
                'vehicle_type' => $row->vehicle_type ?? '',
                'month' => $monthMap[$row->ClaimMonth] ?? 'N/A',
                'upload_date' => $row->CrDate ?? '',
                'bill_date' => $row->BillDate ?? '',
                'claimed_amt' => $row->FilledTAmt ?? 0,
                'FilledAmt' => $row->FilledTAmt ?? '',
                'FilledDate' => $row->FilledDate ?? '',
                'odomtr_opening' => $row->odomtr_opening ?? '',
                'odomtr_closing' => $row->odomtr_closing ?? '',
                'TotKm' => $row->TotKm ?? 0,
                'WType' => $wheelerMap[$row->WType] ?? 'N/A',
                'RatePerKM' => $row->RatePerKM ?? 0,
                'VerifyAmt' => $row->VerifyTAmt ?? 0,
                'VerifyTRemark' => $row->VerifyTRemark ?? 'N/A',
                'VerifyDate' => $row->VerifyDate ?? '',
                'ApprAmt' => $row->ApprTAmt ?? 0,
                'ApprTRemark' => $row->ApprTRemark ?? 'N/A',
                'ApprDate' => $row->ApprDate ?? '',
                'FinancedAmt' => $row->FinancedTAmt ?? 0,
                'FinancedTRemark' => $row->FinancedTRemark ?? 'N/A',
                'FinancedDate' => $row->FinancedDate ?? '',
                default => '',
            };

            if (in_array($column, ['claimed_amt', 'FilledAmt', 'TotKm', 'RatePerKM', 'VerifyAmt', 'ApprAmt', 'FinancedAmt']) && is_numeric($value)) {
                $this->totals[$index] = ($this->totals[$index] ?? 0) + $value;
            }

            $data[] = $value;
        }

        return $data;
    }

    public function getSelectedColumns()
    {
        $columnMap = [
            'sn' => 'e.ExpId',
            'claim_id' => 'e.ClaimId',
            'claim_type' => 'ct.ClaimName AS claim_type_name',
            'claim_status' => 'e.ClaimAtStep',
            'emp_name' => "CONCAT(emp.Fname, ' ', COALESCE(emp.Sname, ''), ' ', emp.Lname) AS employee_name",
            'emp_code' => 'emp.EmpCode AS employee_code',
            'function' => 'f.function_name',
            'vertical' => 'v.vertical_name',
            'department' => 'd.department_name',
            'policy' => 'p.PolicyName AS policy_name',
            'vehicle_type' => 'ee.VehicleType AS vehicle_type',
            'month' => 'e.ClaimMonth',
            'upload_date' => 'e.CrDate',
            'bill_date' => 'e.BillDate',
            'claimed_amt' => 'e.FilledTAmt',
            'FilledAmt' => 'e.FilledTAmt',
            'FilledDate' => 'e.FilledDate',
            'odomtr_opening' => 'e.odomtr_opening',
            'odomtr_closing' => 'e.odomtr_closing',
            'TotKm' => 'e.TotKm',
            'WType' => 'e.WType',
            'RatePerKM' => 'e.RatePerKM',
            'VerifyAmt' => 'e.VerifyTAmt',
            'VerifyTRemark' => 'e.VerifyTRemark',
            'VerifyDate' => 'e.VerifyDate',
            'ApprAmt' => 'e.ApprTAmt',
            'ApprTRemark' => 'e.ApprTRemark',
            'ApprDate' => 'e.ApprDate',
            'FinancedAmt' => 'e.FinancedTAmt',
            'FinancedTRemark' => 'e.FinancedTRemark',
            'FinancedDate' => 'e.FinancedDate',
        ];

        return array_filter(
            array_map(fn($column) => $columnMap[$column] ?? null, $this->columns),
            fn($value) => !is_null($value)
        );
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = chr(65 + count($this->columns) - 1);
        $lastRow = $sheet->getHighestRow();

        // Style for the first row (header)
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF001868']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Style for the data rows (starting from A2)
        if ($lastRow > 1) {
            $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                ],
            ]);
        }

        // Set column widths to auto-size
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = chr(65 + count($this->columns) - 1);
                $totalRow = $lastRow + 1;

                $sheet->setCellValue("A{$totalRow}", 'Total');

                foreach ($this->columns as $index => $column) {
                    if (isset($this->totals[$index])) {
                        $sheet->setCellValue(chr(65 + $index) . $totalRow, $this->totals[$index]);
                    }
                }

                // Style for the totals row (no background color)
                $sheet->getStyle("A{$totalRow}:{$lastColumn}{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                    'alignment' => ['horizontal' => 'right'],
                ]);

                if ($this->protectSheets) {
                    $sheet->getProtection()->setSheet(true);
                    $sheet->getProtection()->setPassword('xai2025');
                }
            },
        ];
    }
}
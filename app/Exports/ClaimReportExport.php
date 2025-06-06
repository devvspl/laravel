<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;

class ClaimReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents, WithTitle
{
    protected $filters;
    protected $columns;
    protected $totals;
    protected $sheetName;

    public function __construct(array $filters, array $columns, string $sheetName = 'Claims')
    {
        $this->filters = $filters;
        $this->columns = $columns;
        $this->totals = [];
        $this->sheetName = $sheetName;
    }

    public function query()
    {
        $selectedColumns = $this->getSelectedColumns();
        $columnsForSelect = array_filter($selectedColumns, fn($col) => $col !== 'e.ExpId');
        $query = DB::table('y7_expenseclaims as e')
            ->leftJoin('claimtype as ct', 'e.ClaimId', '=', 'ct.ClaimId')
            ->join('hrims.hrm_employee as emp', 'e.FilledBy', '=', 'emp.EmployeeID')
            ->join('hrims.hrm_employee_general as eg', 'emp.EmployeeID', '=', 'eg.EmployeeID')
            ->join('hrims.hrm_employee_eligibility as ee', 'emp.EmployeeID', '=', 'ee.EmployeeID')
            ->join('hrims.core_departments as d', 'eg.DepartmentId', '=', 'd.id')
            ->leftJoin('hrims.core_functions as f', 'eg.EmpFunction', '=', 'f.id')
            ->leftJoin('hrims.core_verticals as v', 'eg.EmpVertical', '=', 'v.id')
            ->leftJoin('hrims.hrm_master_eligibility_policy as p', 'ee.VehiclePolicy', '=', 'p.PolicyId')
            ->selectRaw('DISTINCT e.ExpId' . (count($columnsForSelect) ? ',' . implode(',', $columnsForSelect) : ''));

        if (!empty($this->filters['function_ids'])) {
            $query->whereIn('eg.EmpFunction', $this->filters['function_ids']);
        }
        if (!empty($this->filters['vertical_ids'])) {
            $query->whereIn('eg.EmpVertical', $this->filters['vertical_ids']);
        }
        if (!empty($this->filters['department_ids'])) {
            $query->whereIn('eg.DepartmentId', $this->filters['department_ids']);
        }
        if (!empty($this->filters['user_ids'])) {
            $query->whereIn('emp.EmpCode', $this->filters['user_ids']);
        }
        if (!empty($this->filters['months'])) {
            $query->whereIn('e.ClaimMonth', $this->filters['months']);
        }
        if (!empty($this->filters['claim_type_ids'])) {
            if (in_array(7, $this->filters['claim_type_ids'])) {
                $query->where('e.ClaimId', 7);
                if (!empty($this->filters['wheeler_type'])) {
                    $query->where('e.WType', $this->filters['wheeler_type']);
                }
            } else {
                $query->whereIn('e.ClaimId', $this->filters['claim_type_ids']);
            }
        }
        if (!empty($this->filters['policy_ids'])) {
            $query->whereIn('ee.VehiclePolicy', $this->filters['policy_ids']);
        }
        if (!empty($this->filters['vehicle_types'])) {
            $query->whereIn('ee.VehicleType', $this->filters['vehicle_types']);
        }
        if (!empty($this->filters['claim_statuses'])) {
            $query->whereIn('e.ClaimAtStep', $this->filters['claim_statuses']);
        }
        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $dateColumn = match ($this->filters['date_type']) {
                'billDate' => 'e.BillDate',
                'uploadDate' => 'e.CrDate',
                'filledDate' => 'e.FilledDate',
                default => 'e.BillDate',
            };
            $query->whereBetween($dateColumn, [$this->filters['from_date'], $this->filters['to_date']]);
        }

        $query->orderBy('e.ExpId', 'desc');
        return $query;
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
        $monthMap = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
            7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        $wheelerMap = [2 => '2 Wheeler', 4 => '4 Wheeler'];
        $statusMap = [
            1 => 'Draft', 2 => 'Submitted', 3 => 'Filled', 4 => 'Approved', 5 => 'Financed', 6 => 'Payment',
        ];

        $data = [];
        foreach ($this->columns as $index => $column) {
            $value = match ($column) {
                'sn' => $row->ExpId,
                'claim_id' => $row->ClaimId,
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
        $lastColumn = chr(65 + count($this->columns) - 1); // Last column letter (e.g., 'A' to 'Z')
        $lastRow = $sheet->getHighestRow(); // Get actual highest row (1 if only header, 2+ if data)

        // Header row styles (always row 1)
        $sheet->getStyle("A1:${lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => 'FF4B0082'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Apply borders to data rows (rows 2 to lastRow) if data exists
        if ($lastRow > 1) {
            $sheet->getStyle("A2:${lastColumn}${lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
        }

        // Set column widths to auto-size
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set header row height
        $sheet->getRowDimension(1)->setRowHeight(20);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = chr(65 + count($this->columns) - 1);
                $totalRow = $lastRow + 1;

                // Add total row (will be row 2 if no data, or lastRow + 1 if data exists)
                $sheet->setCellValue("A{$totalRow}", 'Total');

                // Calculate and set totals for numeric columns
                foreach ($this->columns as $index => $column) {
                    if (isset($this->totals[$index])) {
                        $sheet->setCellValue(chr(65 + $index) . $totalRow, $this->totals[$index]);
                    }
                }

                // Style total row
                $sheet->getStyle("A{$totalRow}:${lastColumn}{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['argb' => 'FFD3D3D3'],
                    ],
                    'alignment' => ['horizontal' => 'right'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
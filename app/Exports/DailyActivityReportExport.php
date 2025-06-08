<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;

class DailyActivityReportExport implements FromArray, WithHeadings, WithStyles, WithEvents, WithTitle
{
    protected $fromDate;
    protected $toDate;
    protected $data;
    protected $totals;

    public function __construct(string $fromDate, string $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->totals = [
            'total_upload' => 0,
            'punching' => 0,
            'verified' => 0,
            'approved' => 0,
            'financed' => 0,
        ];
        $this->fetchData();
    }

    protected function fetchData()
    {
        $query = "
            SELECT ActionDate,
                SUM(TotalUpload) AS TotalUpload,
                SUM(Punching) AS Punching,
                SUM(Verified) AS Verified,
                SUM(Approved) AS Approved,
                SUM(Financed) AS Financed
            FROM (
                SELECT DATE(CrDate) AS ActionDate, COUNT(*) AS TotalUpload, 0 AS Punching, 0 AS Verified, 0 AS Approved, 0 AS Financed
                FROM y7_expenseclaims
                WHERE CrDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(CrDate) BETWEEN ? AND ?
                GROUP BY DATE(CrDate)

                UNION ALL

                SELECT DATE(FilledDate), 0, COUNT(*), 0, 0, 0
                FROM y7_expenseclaims
                WHERE FilledDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(FilledDate) BETWEEN ? AND ?
                GROUP BY DATE(FilledDate)

                UNION ALL

                SELECT DATE(VerifyDate), 0, 0, COUNT(*), 0, 0
                FROM y7_expenseclaims
                WHERE VerifyDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(VerifyDate) BETWEEN ? AND ?
                GROUP BY DATE(VerifyDate)

                UNION ALL

                SELECT DATE(ApprDate), 0, 0, 0, COUNT(*), 0
                FROM y7_expenseclaims
                WHERE ApprDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(ApprDate) BETWEEN ? AND ?
                GROUP BY DATE(ApprDate)

                UNION ALL

                SELECT DATE(FinancedDate), 0, 0, 0, 0, COUNT(*)
                FROM y7_expenseclaims
                WHERE FinancedDate != '0000-00-00'
                  AND ClaimStatus != 'Deactivate'
                  AND ClaimId NOT IN (19, 20, 21)
                  AND DATE(FinancedDate) BETWEEN ? AND ?
                GROUP BY DATE(FinancedDate)
            ) AS progress
            GROUP BY ActionDate
            ORDER BY ActionDate
        ";

        $this->data = DB::select($query, [
            $this->fromDate, $this->toDate,
            $this->fromDate, $this->toDate,
            $this->fromDate, $this->toDate,
            $this->fromDate, $this->toDate,
            $this->fromDate, $this->toDate,
        ]);

        // Calculate totals
        foreach ($this->data as $row) {
            $this->totals['total_upload'] += $row->TotalUpload;
            $this->totals['punching'] += $row->Punching;
            $this->totals['verified'] += $row->Verified;
            $this->totals['approved'] += $row->Approved;
            $this->totals['financed'] += $row->Financed;
        }
    }

    public function array(): array
    {
        return array_map(function ($row) {
            return [
                $row->ActionDate,
                $row->TotalUpload,
                $row->Punching,
                $row->Verified,
                $row->Approved,
                $row->Financed,
            ];
        }, $this->data);
    }

    public function headings(): array
    {
        return [
            'Action Date',
            'Total Upload',
            'Punching',
            'Verified',
            'Approved',
            'Financed',
        ];
    }

    public function title(): string
    {
        return 'Daily Activity';
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = 'F'; // 6 columns: A to F
        $lastRow = $sheet->getHighestRow(); // After data is written

        // Insert rows for title and summary (7 rows: 1 for title, 5 for summary, 1 blank)
        $sheet->insertNewRowBefore(1, 7);

        // 1. Add Report Title (Row 1)
        $title = "Daily Activity Report - " . date('M Y', strtotime($this->fromDate)) . " to " . date('M Y', strtotime($this->toDate));
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => 'FF001868'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFFFF'],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // 2. Add Summary Section (Rows 2 to 6)
        $summaryData = [
            ['Total Uploads', $this->totals['total_upload']],
            ['Total Punching', $this->totals['punching']],
            ['Total Verified', $this->totals['verified']],
            ['Total Approved', $this->totals['approved']],
            ['Total Financed', $this->totals['financed']],
        ];

        foreach ($summaryData as $index => $row) {
            $rowNum = $index + 2; // Rows 2 to 6
            $sheet->setCellValue("A{$rowNum}", $row[0]);
            $sheet->setCellValue("B{$rowNum}", $row[1]);
        }

        $sheet->getStyle('A2:B6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FF000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE6F0FA'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => 'left',
                'vertical' => 'center',
            ],
        ]);
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        foreach (range(2, 6) as $rowNum) {
            $sheet->getRowDimension($rowNum)->setRowHeight(20);
        }

        // Leave Row 7 blank for spacing
        $sheet->getRowDimension(7)->setRowHeight(10);

        // Style the column header row (now Row 8)
        $sheet->getStyle("A8:{$lastColumn}8")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF299CDB'],
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

        // Apply borders to data rows (rows 9 to lastRow) if data exists
        if ($lastRow > 8) {
            $sheet->getStyle("A9:{$lastColumn}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
        }

        // Set column widths to auto-size for remaining columns
        foreach (range('C', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set row height for headers
        $sheet->getRowDimension(8)->setRowHeight(25);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = 'F';
                $totalRow = $lastRow + 1;

                // Add total row
                $sheet->setCellValue("A{$totalRow}", 'Total');
                $sheet->setCellValue("B{$totalRow}", $this->totals['total_upload']);
                $sheet->setCellValue("C{$totalRow}", $this->totals['punching']);
                $sheet->setCellValue("D{$totalRow}", $this->totals['verified']);
                $sheet->setCellValue("E{$totalRow}", $this->totals['approved']);
                $sheet->setCellValue("F{$totalRow}", $this->totals['financed']);

                // Style total row
                $sheet->getStyle("A{$totalRow}:{$lastColumn}{$totalRow}")->applyFromArray([
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
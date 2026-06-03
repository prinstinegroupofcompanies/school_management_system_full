<?php

namespace App\Services;

use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    /**
     * Export data in various formats.
     */
    public function export($data, $format = 'csv', $columns = [], $filename = null)
    {
        $filename = $filename ?? 'export_' . now()->format('Y-m-d_His');

        return match($format) {
            'csv' => $this->exportCsv($data, $columns, $filename),
            'excel' => $this->exportExcel($data, $columns, $filename),
            'pdf' => $this->exportPdf($data, $columns, $filename),
            default => throw new \InvalidArgumentException("Unsupported export format: {$format}"),
        };
    }

    /**
     * Export as CSV.
     */
    protected function exportCsv($data, $columns, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($data, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write headers
            if (!empty($columns)) {
                fputcsv($file, array_values($columns));
            } else {
                $firstRow = $data->first();
                if ($firstRow) {
                    fputcsv($file, array_keys($firstRow->toArray()));
                }
            }

            // Write data
            foreach ($data as $row) {
                $rowData = [];
                if (!empty($columns)) {
                    foreach (array_keys($columns) as $key) {
                        $rowData[] = $this->getValue($row, $key);
                    }
                } else {
                    $rowData = $row->toArray();
                }
                fputcsv($file, $rowData);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export as Excel.
     */
    protected function exportExcel($data, $columns, $filename)
    {
        $export = new class($data, $columns) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;
            protected $columns;

            public function __construct($data, $columns)
            {
                $this->data = $data;
                $this->columns = $columns;
            }

            public function collection()
            {
                return $this->data->map(function($row) {
                    $rowData = [];
                    foreach (array_keys($this->columns) as $key) {
                        $rowData[$key] = $this->getValue($row, $key);
                    }
                    return collect($rowData);
                });
            }

            public function headings(): array
            {
                return array_values($this->columns);
            }

            protected function getValue($row, $key)
            {
                if (str_contains($key, '.')) {
                    return data_get($row, $key, '');
                }
                return $row->{$key} ?? '';
            }
        };

        return Excel::download($export, "{$filename}.xlsx");
    }

    /**
     * Export as PDF.
     */
    protected function exportPdf($data, $columns, $filename)
    {
        $pdf = Pdf::loadView('exports.pdf.default', [
            'data' => $data,
            'columns' => $columns,
            'title' => ucfirst($filename),
        ]);

        return $pdf->download("{$filename}.pdf");
    }

    /**
     * Get value for a field.
     */
    protected function getValue($row, $key)
    {
        if (str_contains($key, '.')) {
            return data_get($row, $key, '');
        }
        return $row->{$key} ?? '';
    }
}


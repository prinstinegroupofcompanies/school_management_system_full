<?php

namespace App\Traits;

use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

trait Exportable
{
    /**
     * Export data in various formats.
     */
    public function export($format = 'csv', $data = null, $columns = null, $filename = null)
    {
        $data = $data ?? $this->getExportData();
        $columns = $columns ?? $this->exportColumns ?? [];
        $filename = $filename ?? $this->getExportFilename();

        return match($format) {
            'csv' => $this->exportCsv($data, $columns, $filename),
            'excel' => $this->exportExcel($data, $columns, $filename),
            'pdf' => $this->exportPdf($data, $columns, $filename),
            default => throw new \InvalidArgumentException("Unsupported export format: {$format}"),
        };
    }

    /**
     * Get data for export.
     */
    protected function getExportData()
    {
        if (isset($this->exportRelations) && is_array($this->exportRelations)) {
            return $this->with($this->exportRelations)->get();
        }
        return $this->get();
    }

    /**
     * Get export filename.
     */
    protected function getExportFilename(): string
    {
        $modelName = class_basename($this);
        return strtolower($modelName) . '_' . now()->format('Y-m-d_His');
    }

    /**
     * Export as CSV.
     */
    protected function exportCsv($data, $columns, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($data, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write headers
            if (!empty($columns)) {
                fputcsv($file, array_keys($columns));
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
                    foreach ($columns as $key => $label) {
                        $rowData[] = $this->getExportValue($row, $key);
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
                return $this->data;
            }

            public function headings(): array
            {
                if (!empty($this->columns)) {
                    return array_keys($this->columns);
                }
                $firstRow = $this->data->first();
                return $firstRow ? array_keys($firstRow->toArray()) : [];
            }
        };

        return Excel::download($export, "{$filename}.xlsx");
    }

    /**
     * Export as PDF.
     */
    protected function exportPdf($data, $columns, $filename)
    {
        $pdfView = $this->pdfView ?? 'exports.pdf.default';
        
        $pdf = Pdf::loadView($pdfView, [
            'data' => $data,
            'columns' => $columns,
            'title' => $this->getExportTitle() ?? 'Export',
        ]);

        return $pdf->download("{$filename}.pdf");
    }

    /**
     * Get export value for a field.
     */
    protected function getExportValue($row, $key)
    {
        if (str_contains($key, '.')) {
            return data_get($row, $key, '');
        }
        return $row->{$key} ?? '';
    }

    /**
     * Get export title.
     */
    protected function getExportTitle(): ?string
    {
        return null;
    }
}


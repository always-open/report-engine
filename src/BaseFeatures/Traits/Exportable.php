<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Traits;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Cell;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Row;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

trait Exportable
{
    private static $EXCEL_FORMAT = 'xlsx';
    private static $PDF_FORMAT = 'pdf';
    private static $CSV_FORMAT = 'csv';

    /**
     * @return string
     */
    public function exportTitle(): string
    {
        return $this->title() . ' - ' . Carbon::now()->toDayDateTimeString();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function toCsv()
    {
        $writer = new CsvWriter($this->buildSpreadsheet());

        return $this->downloadSpreadsheet(
            $writer,
            $this->exportTitle() . '.' . self::$CSV_FORMAT,
            'text/csv',
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function toXlsx()
    {
        $writer = new XlsxWriter($this->buildSpreadsheet());

        return $this->downloadSpreadsheet(
            $writer,
            $this->exportTitle() . '.' . self::$EXCEL_FORMAT,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function toPdf()
    {
        $writer = new Dompdf($this->buildSpreadsheet());

        return $this->downloadSpreadsheet(
            $writer,
            $this->exportTitle() . '.' . self::$PDF_FORMAT,
            'application/pdf',
        );
    }

    /**
     * Build the response for spreadsheets of all types (csv, xls, xlsx, pdf).
     */
    protected function buildSpreadsheet(): Spreadsheet
    {
        $this->build();

        $data = $this->rows->map(function (Row $row) {
            return $row->getCells()->mapWithKeys(function (Cell $cell) {
                return [$cell->getColumn()->label() => $cell->getExportValue()];
            });
        });

        $columns = $data->first()->keys()->toArray();

        $data = $data->toArray();

        array_unshift($data, $columns);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                        ->setCreator('SupplyBrain')
                        ->setTitle($this->title());

        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($data)
              ->setShowGridlines(true);

        foreach ($columns as $key => $row) {
            $dimension = $sheet->getColumnDimensionByColumn($key);
            $dimension->setAutoSize(true);
        }

        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4)
                              ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->getPageMargins()->setTop(0.125)
                                ->setRight(0.125)
                                ->setLeft(0.125)
                                ->setBottom(0.125);

        return $spreadsheet;
    }

    /**
     * @param        $writer
     * @param string $filename
     * @param string $contentType
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function downloadSpreadsheet($writer, string $filename, string $contentType)
    {
        $callback = function () use ($writer) {
            $writer->save('php://output');
        };

        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT', // Date in the past
            'Last-Modified' => Carbon::now()->format('D, d M Y H:i:s'),
            'Cache-Control' => 'cache, must-revalidate',
            'Pragma' => 'public',
        ];

        return response()->stream($callback, 200, $headers);
    }

    /**
     * @return bool
     */
    public function isExporting(): bool
    {
        return in_array($this->getCurrentRequest()->route('_format'), [
            self::$CSV_FORMAT,
            self::$PDF_FORMAT,
            self::$EXCEL_FORMAT,
        ]);
    }
}

<?php

namespace App\Exports;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExporter
{
    /**
     * Export data as XLSX streamed download.
     *
     * @param string $filename
     * @param array $headers  Column headers
     * @param iterable $rows  Data rows (arrays or Collections)
     */
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return new StreamedResponse(function () use ($headers, $rows) {
            $writer = new Writer();
            $writer->openToOutput();

            // Header row
            $writer->addRow(Row::fromValues($headers));

            // Data rows
            foreach ($rows as $row) {
                $values = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array) $row);
                $writer->addRow(Row::fromValues(array_values($values)));
            }

            $writer->close();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

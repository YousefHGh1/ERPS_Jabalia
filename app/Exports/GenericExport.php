<?php

namespace App\Exports;

use Illuminate\Support\Collection;

/**
 * غلاف بسيط لبيانات التصدير (بدون أي اعتماد على حزم خارجية).
 *
 * ملاحظة: التصدير الفعلي إلى xlsx يتم عبر PhpSpreadsheet مباشرة داخل
 * App\Http\Controllers\Settings\ExportController.
 */
class GenericExport
{
    protected Collection $data;
    protected array $headings;

    public function __construct(array $data, array $headings = null)
    {
        $this->data = collect($data);
        $this->headings = $headings ?? array_keys($data[0] ?? []);
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($row): array
    {
        return (array) $row;
    }

    public function toArray(): array
    {
        return $this->data->map(fn ($row) => (array) $row)->all();
    }
}


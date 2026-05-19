<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        public ?string $status = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {}

    public function query()
    {
        return Payment::query()
            ->with(['serviceRequest.student', 'serviceRequest.provider'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->startDate, fn ($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Reference', 'Request #', 'Student', 'Provider',
            'Amount (₵)', 'Method', 'Status', 'Paid At', 'Date',
        ];
    }

    public function map($row): array
    {
        return [
            $row->reference,
            $row->serviceRequest?->request_number,
            $row->serviceRequest?->student?->name,
            $row->serviceRequest?->provider?->business_name ?? '—',
            number_format($row->amount, 2),
            $row->method ?? '—',
            ucfirst($row->status),
            $row->paid_at?->format('Y-m-d H:i') ?? '—',
            $row->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

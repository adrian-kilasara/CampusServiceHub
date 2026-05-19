<?php

namespace App\Exports;

use App\Models\ServiceRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceRequestsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        public ?string $status = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {}

    public function query()
    {
        return ServiceRequest::query()
            ->with(['student', 'provider', 'service.category', 'payment'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->startDate, fn ($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Request #', 'Title', 'Student', 'Provider',
            'Service', 'Category', 'Urgency', 'Status',
            'Amount (₵)', 'Payment Status', 'Submitted', 'Completed',
        ];
    }

    public function map($row): array
    {
        return [
            $row->request_number,
            $row->title,
            $row->student?->name,
            $row->provider?->business_name ?? 'Unassigned',
            $row->service?->name,
            $row->service?->category?->name,
            ucfirst($row->urgency),
            str_replace('_', ' ', ucfirst($row->status)),
            $row->final_price ?? $row->quoted_price ?? '—',
            $row->payment ? ucfirst($row->payment->status) : '—',
            $row->created_at->format('Y-m-d H:i'),
            $row->completed_at?->format('Y-m-d H:i') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\DocumentRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class DocumentRequestsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    
    public function __construct(private int $userId, private bool $isPemohon) {}

    public function query()
    {
        $query = DocumentRequest::query()->with('applicant')->latest('submitted_at');

        if ($this->isPemohon) {
            $query->whereHas('applicant', fn($q) => $q->where('user_id', $this->userId));
        }

        return $query;
    }

    public function headings(): array
    {
        return ['ID', 'Document Type', 'Applicant', 'Status', 'Submitted At', 'Decided At', 'Note'];
    }

    public function map($request): array
    {
        return [
            $request->id,
            $request->document_type,
            $request->applicant->company_name,
            $request->status,
            $request->submitted_at,
            $request->decided_at,
            $request->note,
        ];
    }
}

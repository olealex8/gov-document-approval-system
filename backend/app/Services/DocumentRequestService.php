<?php

namespace App\Services;

use App\Models\DocumentRequest;
use App\Models\RequestHistory;
use Illuminate\Support\Facades\DB;

class DocumentRequestService
{
    public function create(array $data, int $applicantId, int $userId): DocumentRequest
    {
        return DB::transaction(function () use ($data, $applicantId, $userId) {
            $request = DocumentRequest::create([
                'applicant_id' => $applicantId,
                'document_type' => $data['document_type'],
                'status' => 'submitted',
                'submitted_at' => now(),
                'note' => $data['note'] ?? null,
            ]);

            RequestHistory::create([
                'document_request_id' => $request->id,
                'from_status' => null,
                'to_status' => 'submitted',
                'changed_by' => $userId,
            ]);

            return $request;
        });
    }

    public function decide(DocumentRequest $documentRequest, string $decision, ?string $note, int $userId): DocumentRequest
    {
        return DB::transaction(function () use ($documentRequest, $decision, $note, $userId) {
            $from = $documentRequest->status;

            $documentRequest->update([
                'status' => $decision,
                'decided_by' => $userId,
                'decided_at' => now(),
                'note' => $note ?? $documentRequest->note,
            ]);

            RequestHistory::create([
                'document_request_id' => $documentRequest->id,
                'from_status' => $from,
                'to_status' => $decision,
                'changed_by' => $userId,
                'note' => $note,
            ]);

            return $documentRequest;
        });
    }

    public function resubmit(DocumentRequest $documentRequest, array $data, int $userId): DocumentRequest
    {
        return DB::transaction(function () use ($documentRequest, $data, $userId) {
            $from = $documentRequest->status;

            $documentRequest->update([
                'status' => 'submitted',
                'document_type' => $data['document_type'] ?? $documentRequest->document_type,
                'note' => $data['note'] ?? $documentRequest->note,
                'submitted_at' => now(),
            ]);

            RequestHistory::create([
                'document_request_id' => $documentRequest->id,
                'from_status' => $from,
                'to_status' => 'submitted',
                'changed_by' => $userId,
                'note' => 'Resubmitted after revision',
            ]);

            return $documentRequest;
        });
    }
}
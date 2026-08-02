<?php

namespace App\Policies;

use App\Models\DocumentRequest;
use App\Models\User;

class DocumentRequestPolicy
{
    public function decide(User $user, DocumentRequest $documentRequest): bool
    {
        return $user->hasRole('penguji') && $documentRequest->status === 'submitted';
    }

    public function resubmit(User $user, DocumentRequest $documentRequest): bool
    {
        return $user->id === $documentRequest->applicant->user_id
            && $documentRequest->status === 'revision';
    }

    public function view(User $user, DocumentRequest $documentRequest): bool
    {
        return $user->hasRole('penguji')
            || $user->id === $documentRequest->applicant->user_id;
    }

    public function uploadFile(User $user, DocumentRequest $documentRequest): bool
    {
        return $user->id === $documentRequest->applicant->user_id
            && in_array($documentRequest->status, ['submitted', 'revision']);
    }

    public function deleteFile(User $user, DocumentRequest $documentRequest): bool
    {
        return $user->id === $documentRequest->applicant->user_id
            && $documentRequest->status === 'revision';
    }
}
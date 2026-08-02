<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequestRequest;
use App\Http\Requests\DecideDocumentRequestRequest;
use App\Http\Resources\DocumentRequestResource;
use App\Models\DocumentRequest;
use App\Services\DocumentRequestService;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    private DocumentRequestService $service;

    public function __construct(DocumentRequestService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = DocumentRequest::with('applicant')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('submitted_at');

        if ($request->user()->hasRole('pemohon')) {
            $query->whereHas('applicant', fn($q) => $q->where('user_id', $request->user()->id));
        }

        return DocumentRequestResource::collection($query->paginate(15));
    }

    public function show(DocumentRequest $documentRequest)
    {
        $this->authorize('view', $documentRequest);
        $documentRequest->load(['applicant', 'histories', 'files']);
        return new DocumentRequestResource($documentRequest);
    }

    public function store(StoreDocumentRequestRequest $request)
    {
        $applicantId = $request->user()->applicant->id;
        $userId = $request->user()->id;
        $doc = $this->service->create($request->validated(), $applicantId, $userId);

        return new DocumentRequestResource($doc);
    }

    public function decide(DecideDocumentRequestRequest $request, DocumentRequest $documentRequest)
    {
        $this->authorize('decide', $documentRequest);
        $doc = $this->service->decide(
            $documentRequest,
            $request->validated('decision'),
            $request->validated('note'),
            $request->user()->id
        );

        return new DocumentRequestResource($doc);
    }

    public function resubmit(StoreDocumentRequestRequest $request, DocumentRequest $documentRequest)
    {
        $this->authorize('resubmit', $documentRequest); 

        $doc = $this->service->resubmit($documentRequest, $request->validated(), $request->user()->id);

        return new DocumentRequestResource($doc);
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDocumentFileRequest;
use App\Models\DocumentFile;
use App\Models\DocumentRequest;

class DocumentFileController extends Controller
{
    public function store(UploadDocumentFileRequest $request, DocumentRequest $documentRequest)
    {
        $this->authorize('uploadFile', $documentRequest); 

        $file = $request->file('file');
        $path = $file->store("document-requests/{$documentRequest->id}", 'public');

        $documentFile = DocumentFile::create([
            'document_request_id' => $documentRequest->id,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json($documentFile, 201);
    }

    public function destroy(DocumentFile $documentFile)
    {
        $this->authorize('deleteFile', $documentFile->documentRequest);

        \Storage::disk('public')->delete($documentFile->path);
        $documentFile->delete();

        return response()->json(['message' => 'File deleted']);
    }
}

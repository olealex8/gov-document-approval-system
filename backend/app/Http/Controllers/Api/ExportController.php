<?php

namespace App\Http\Controllers\Api;

use App\Exports\DocumentRequestsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function excel(Request $request)
    {
        $isPemohon = $request->user()->hasRole('pemohon');

        return Excel::download(
            new DocumentRequestsExport($request->user()->id, $isPemohon),
            'document-requests.xlsx'
        );
    }
}
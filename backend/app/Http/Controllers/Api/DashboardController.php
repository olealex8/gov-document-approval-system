<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $user = $request->user();
        $isPemohon = $user->hasRole('pemohon');

        $cacheKey = $isPemohon ? "dashboard-summary-user-{$user->id}" : 'dashboard-summary-all';

        $data = Cache::remember($cacheKey, 60, function () use ($isPemohon, $user) {
            $baseQuery = DocumentRequest::query();

            if ($isPemohon) {
                $baseQuery->whereHas('applicant', fn($q) => $q->where('user_id', $user->id));
            }

            return [
                'total' => (clone $baseQuery)->count(),
                'by_status' => (clone $baseQuery)
                    ->selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'recent_30_days' => (clone $baseQuery)
                    ->where('submitted_at', '>=', now()->subDays(30))
                    ->selectRaw('DATE(submitted_at) as date, count(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ];
        });

        return response()->json($data);
    }
}

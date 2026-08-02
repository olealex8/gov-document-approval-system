<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\DocumentRequest;
use App\Models\RequestHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LargeDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding 1000 penguji users...');
        User::factory()
            ->count(1000)
            ->create()
            ->each(fn($user) => $user->assignRole('penguji'));

        $this->command->info('Seeding 1000 pemohon users with applicant profiles...');
        $applicantIds = [];

        User::factory()
            ->count(1000)
            ->create()
            ->each(function ($user) use (&$applicantIds) {
                $user->assignRole('pemohon');
                $applicant = Applicant::factory()->create(['user_id' => $user->id]);
                $applicantIds[] = $applicant->id;
            });

        $this->command->info('Seeding 10,000 document requests...');
        $pengujiIds = User::role('penguji')->pluck('id');

        DocumentRequest::factory()
            ->count(10000)
            ->make()
            ->each(function ($request) use ($applicantIds, $pengujiIds) {
                $request->applicant_id = fake()->randomElement($applicantIds);
                if (in_array($request->status, ['approved', 'rejected'])) {
                    $request->decided_by = fake()->randomElement($pengujiIds->toArray());
                }
                $request->save();

                RequestHistory::create([
                    'document_request_id' => $request->id,
                    'from_status' => null,
                    'to_status' => 'submitted',
                    'changed_by' => $request->applicant->user_id,
                    'created_at' => $request->submitted_at,
                ]);

                if ($request->status !== 'submitted') {
                    RequestHistory::create([
                        'document_request_id' => $request->id,
                        'from_status' => 'submitted',
                        'to_status' => $request->status,
                        'changed_by' => $request->decided_by ?? fake()->randomElement($pengujiIds->toArray()),
                        'created_at' => $request->decided_at ?? $request->submitted_at,
                    ]);
                }
            });

        $this->command->info('Done seeding large dataset.');
    }
}
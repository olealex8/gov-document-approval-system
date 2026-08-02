<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE document_requests DROP CONSTRAINT document_requests_status_check");
        DB::statement("ALTER TABLE document_requests ADD CONSTRAINT document_requests_status_check CHECK (status IN ('submitted', 'revision', 'approved', 'rejected'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

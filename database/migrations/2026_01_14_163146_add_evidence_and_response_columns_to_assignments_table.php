<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Reporter Evidence (Separated)
            $table->string('evidence_photo')->nullable()->after('evidence_link'); // Path to photo
            $table->string('evidence_document')->nullable()->after('evidence_photo'); // Path to doc/pdf

            // Staff Response
            $table->string('staff_response_file')->nullable()->after('priority'); // File related to feedback
            $table->text('staff_response_note')->nullable()->after('staff_response_file'); // Text feedback
            $table->dateTime('staff_response_at')->nullable()->after('staff_response_note'); // Timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn([
                'evidence_photo',
                'evidence_document',
                'staff_response_file',
                'staff_response_note',
                'staff_response_at'
            ]);
        });
    }
};

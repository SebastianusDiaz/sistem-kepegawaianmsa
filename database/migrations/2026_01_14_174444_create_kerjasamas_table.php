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
        Schema::create('kerjasamas', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->date('start_date');
            $table->date('end_date');

            // Representative (Perwakilan Perusahaan)
            $table->string('representative_name');
            $table->string('representative_phone')->nullable();
            $table->string('representative_email')->nullable();

            // Penanggung Jawab (PIC from karyawan)
            $table->foreignId('pic_id')->constrained('users')->onDelete('cascade');

            // File MoU
            $table->string('file_path')->nullable();

            // Approval Status
            $table->enum('status', ['pending', 'active', 'rejected', 'expired'])->default('pending');
            $table->text('rejection_note')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasamas');
    }
};

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
        Schema::table('absensis', function (Blueprint $table) {
            $table->renameColumn('status', 'legacy_status');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->enum('attendance_type', ['office', 'field'])->default('office')->after('user_id');
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->nullOnDelete()->after('attendance_type');
            $table->decimal('lat', 10, 8)->nullable()->after('jam_keluar');
            $table->decimal('lng', 11, 8)->nullable()->after('lat');
            $table->decimal('accuracy', 8, 2)->nullable()->after('lng');
            $table->integer('worked_minutes')->nullable()->after('accuracy');
            $table->enum('status', ['open', 'closed', 'auto_closed', 'manual_edit'])->default('open')->after('worked_minutes');
            $table->text('note')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['attendance_type', 'assignment_id', 'lat', 'lng', 'accuracy', 'worked_minutes', 'status', 'note']);
            $table->renameColumn('legacy_status', 'status');
        });
    }
};

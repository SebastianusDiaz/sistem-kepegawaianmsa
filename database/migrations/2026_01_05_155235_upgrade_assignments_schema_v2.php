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
        // 1. Drop Foreign Key in absensis first
        Schema::table('absensis', function (Blueprint $table) {
            // Need to know the exact constraint name, usually 'absensis_assignment_id_foreign'
            // But to be safe, we drop the column if we are replacing it, or just the foreign key.
            // Since we are changing type from Int to UUID, we must drop column and re-add.
            $table->dropForeign(['assignment_id']);
            $table->dropColumn('assignment_id');
        });

        // 2. Drop old assignments table
        Schema::dropIfExists('assignments');

        // 3. Create new assignments table with UUID
        Schema::create('assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('editor_id')->constrained('users');
            $table->foreignId('reporter_id')->constrained('users');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('location_name');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->dateTime('start_time');
            $table->dateTime('deadline');
            $table->enum('status', ['draft', 'assigned', 'accepted', 'on_site', 'submitted', 'published', 'canceled'])->default('draft');
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');
            $table->softDeletes();
            $table->timestamps();
        });

        // 4. Add assignment_id back to absensis as UUID
        Schema::table('absensis', function (Blueprint $table) {
            $table->foreignUuid('assignment_id')->nullable()->after('attendance_type')->constrained('assignments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropColumn('assignment_id');
        });

        Schema::dropIfExists('assignments');

        // Recreation of old table (Simplified for rollback)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('status');
            $table->timestamps();
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->foreignId('assignment_id')->nullable()->constrained('assignments');
        });
    }
};

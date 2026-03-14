<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL: Alter ENUM to add 'revision' status
        DB::statement("ALTER TABLE assignments MODIFY COLUMN status ENUM('draft', 'assigned', 'accepted', 'on_site', 'submitted', 'revision', 'published', 'canceled') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM (remove 'revision')
        DB::statement("ALTER TABLE assignments MODIFY COLUMN status ENUM('draft', 'assigned', 'accepted', 'on_site', 'submitted', 'published', 'canceled') DEFAULT 'draft'");
    }
};

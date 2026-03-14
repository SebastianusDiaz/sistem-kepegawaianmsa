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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('birth_place')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            $table->string('signature_path')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['birth_place', 'birth_date', 'gender', 'signature_path']);
        });
    }
};

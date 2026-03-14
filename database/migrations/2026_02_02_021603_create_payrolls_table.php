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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');

            // Salary Components
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('allowances', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);

            // Deductions
            $table->decimal('deductions', 15, 2)->default(0);
            $table->json('deduction_details')->nullable(); // Store details like {alpha: 2, izin: 1}

            $table->decimal('net_salary', 15, 2)->default(0);
            $table->enum('status', ['draft', 'paid', 'cancelled'])->default('draft');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};

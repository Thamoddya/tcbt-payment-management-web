<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('students_id')
                ->constrained('students')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('invoice_number');
            $table->decimal('amount')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->foreignId("processed_by")
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->enum('paid_month', [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ])->nullable();
            $table->enum('paid_year', [
                '2023',
                '2024',
                '2025',
                '2026',
            ])->nullable();
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

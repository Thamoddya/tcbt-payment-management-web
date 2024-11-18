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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('tcbt_student_number')->unique();
            $table->string('name');
            $table->string('contact_no')->nullable();
            $table->integer('need_to_pay')->default(0);
            $table->string('grade');
            $table->string('school');
            $table->string('address')->nullable();
            $table->string('parent_contact_no')->nullable();
            $table->string('parent_name')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the 'test_cases' table to store test case details.
     */
    public function up(): void
    {
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('section');
            $table->string('test_case_id')->unique();
            $table->string('test_title');
            $table->longText('description');
            $table->integer('test_status')->default(0)->nullable(); // 0 for pending, 1 for pass, 2 for fail
            $table->longText('comments')->nullable();
            $table->foreignId('tested_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'test_cases' table.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_cases');
    }
};

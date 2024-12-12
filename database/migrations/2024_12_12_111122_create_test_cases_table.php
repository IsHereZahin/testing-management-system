<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('test_case_id');
            $table->text('description');
            $table->text('steps');
            $table->text('expected_result');
            $table->string('step_status')->default('Not tested');
            $table->string('test_status')->default('Not tested');
            $table->text('comments')->nullable();
            $table->timestamp('last_tested')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_cases');
    }
};

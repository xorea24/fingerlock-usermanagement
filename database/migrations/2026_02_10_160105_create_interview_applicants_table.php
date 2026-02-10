<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('interview_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->nullable()->constrained('interviews');
            $table->foreignId('applicant_id')->nullable()->constrained('applicants');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_applicants');
    }
};
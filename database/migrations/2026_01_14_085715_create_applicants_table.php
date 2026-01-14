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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('present_position_id')->nullable()->constrained('positions');
            $table->foreignId('desired_position_id')->nullable()->constrained('positions');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('name_suffix', 50);            
            $table->string('gender', 1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};

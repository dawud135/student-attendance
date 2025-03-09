<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('school_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // E.g., M001, S002, H003
            $table->string('name'); // E.g., Math, Science, History
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('school_subjects');
    }
};

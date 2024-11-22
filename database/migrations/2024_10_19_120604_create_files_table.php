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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->enum('status', ['free', 'checked_in'])->default('free');
            $table->unsignedBigInteger('checked_in_by')->nullable();
            $table->timestamp('checked_in_at')->nullable();

            // $table->foreignId('groub_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('checked_in_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};

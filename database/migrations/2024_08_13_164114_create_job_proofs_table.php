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
        Schema::create('job_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('proof_answer');
            $table->json('proof_photos');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Reviewed'])->default('Pending');
            $table->text('rejected_reason')->nullable();
            $table->text('reviewed_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->integer('approved_by')->nullable();
            $table->integer('rejected_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_proofs');
    }
};

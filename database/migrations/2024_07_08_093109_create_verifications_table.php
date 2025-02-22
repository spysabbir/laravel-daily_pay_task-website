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
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('id_type', ['NID', 'Passport', 'Driving License']);
            $table->string('id_number');
            $table->string('id_front_image');
            $table->string('id_with_face_image');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('rejected_reason')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->foreignId('rejected_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            // Ensure unique constraint for id_type and id_number together
            $table->unique(['id_type', 'id_number']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};

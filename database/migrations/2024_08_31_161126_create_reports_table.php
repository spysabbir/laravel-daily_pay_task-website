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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['User', 'Post Task', 'Proof Task'])->default('User');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_task_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('proof_task_id')->nullable()->constrained()->onDelete('cascade');
            $table->longText('reason');
            $table->string('photo')->nullable();
            $table->enum('status', ['Pending', 'False', 'Received'])->default('Pending');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

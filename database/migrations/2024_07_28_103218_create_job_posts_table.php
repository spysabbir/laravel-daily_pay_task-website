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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('child_category_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('required_proof');
            $table->text('additional_note');
            $table->string('thumbnail')->nullable();
            $table->integer('need_worker');
            $table->integer('worker_charge');
            $table->integer('extra_screenshots');
            $table->integer('job_boosted_time');
            $table->integer('job_running_day');
            $table->enum('status', ['Pending', 'Rejected', 'Running', 'Canceled', 'Paused', 'Completed']);
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->integer('rejected_by')->nullable();
            $table->integer('approved_by')->nullable();
            $table->integer('canceled_by')->nullable();
            $table->integer('paused_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('pause_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};

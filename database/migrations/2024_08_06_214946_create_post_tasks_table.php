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
        Schema::create('post_tasks', function (Blueprint $table) {
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
            $table->integer('work_needed');
            $table->decimal('earnings_from_work', 8, 2)->default(0);
            $table->integer('extra_screenshots');
            $table->integer('boosted_time');
            $table->integer('running_day');
            $table->decimal('charge', 8, 2)->default(0);
            $table->decimal('site_charge', 8, 2)->default(0);
            $table->decimal('total_charge', 8, 2)->default(0);
            $table->enum('status', ['Pending', 'Running', 'Rejected', 'Canceled', 'Paused', 'Completed']);
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->integer('approved_by')->nullable();
            $table->integer('rejected_by')->nullable();
            $table->integer('canceled_by')->nullable();
            $table->integer('paused_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tasks');
    }
};

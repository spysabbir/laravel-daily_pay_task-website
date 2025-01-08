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
        Schema::create('default_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('referral_registration_bonus_amount', 10, 2)->nullable();
            $table->decimal('referral_withdrawal_bonus_percentage', 10, 2)->nullable();
            $table->string('deposit_bkash_account')->nullable();
            $table->string('deposit_rocket_account')->nullable();
            $table->string('deposit_nagad_account')->nullable();
            $table->decimal('min_deposit_amount', 10, 2)->nullable();
            $table->decimal('max_deposit_amount', 10, 2)->nullable();
            $table->decimal('deposit_balance_from_withdraw_balance_charge_percentage', 10, 2)->nullable();
            $table->decimal('withdraw_balance_from_deposit_balance_charge_percentage', 10, 2)->nullable();
            $table->decimal('instant_withdraw_charge', 10, 2)->nullable();
            $table->decimal('withdraw_charge_percentage', 10, 2)->nullable();
            $table->decimal('min_withdraw_amount', 10, 2)->nullable();
            $table->decimal('max_withdraw_amount', 10, 2)->nullable();
            $table->decimal('task_posting_charge_percentage', 10, 2)->nullable();
            $table->decimal('task_posting_additional_required_proof_photo_charge', 10, 2)->nullable();
            $table->decimal('task_posting_boosting_time_charge', 10, 2)->nullable();
            $table->decimal('task_posting_additional_work_duration_charge', 10, 2)->nullable();
            $table->decimal('task_posting_min_budget', 10, 2)->nullable();
            $table->decimal('posted_task_proof_submit_user_max_bonus_amount', 10, 2)->nullable();
            $table->integer('posted_task_proof_submit_auto_approved_time')->nullable();
            $table->integer('posted_task_proof_submit_rejected_charge_auto_refund_time')->nullable();
            $table->decimal('rejected_worked_task_review_charge', 10, 2)->nullable();
            $table->integer('user_max_blocked_time_for_banned')->nullable();
            $table->decimal('user_blocked_instant_resolved_charge', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_settings');
    }
};

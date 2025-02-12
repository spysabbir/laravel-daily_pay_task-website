@extends('layouts.template_master')

@section('title', 'Default Setting')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Default Setting</h3>
                <p><span class="text-danger">*</span> <small>Fields are required</small></p>
            </div>
            <div class="card-body">
                <form class="forms-sample" action="{{ route('backend.default.setting.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="referral_registration_bonus_amount" class="form-label">Referral Registration Bonus Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="referral_registration_bonus_amount" name="referral_registration_bonus_amount" value="{{ old('referral_registration_bonus_amount', $defaultSetting->referral_registration_bonus_amount) }}" placeholder="Referral Registration Bonus Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('referral_registration_bonus_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="referral_withdrawal_bonus_percentage" class="form-label">Referral Withdrawal Bonus Percentage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="referral_withdrawal_bonus_percentage" name="referral_withdrawal_bonus_percentage" value="{{ old('referral_withdrawal_bonus_percentage', $defaultSetting->referral_withdrawal_bonus_percentage) }}" placeholder="Referral Withdrawal Bonus Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('referral_withdrawal_bonus_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="deposit_bkash_account" class="form-label">Deposit Bkash Account <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="deposit_bkash_account" name="deposit_bkash_account" value="{{ old('deposit_bkash_account', $defaultSetting->deposit_bkash_account) }}" placeholder="Deposit Bkash Account">
                            @error('deposit_bkash_account')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="deposit_rocket_account" class="form-label">Deposit Rocket Account <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="deposit_rocket_account" name="deposit_rocket_account" value="{{ old('deposit_rocket_account', $defaultSetting->deposit_rocket_account) }}" placeholder="Deposit Rocket Account">
                            @error('deposit_rocket_account')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="deposit_nagad_account" class="form-label">Deposit Nagad Account <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="deposit_nagad_account" name="deposit_nagad_account" value="{{ old('deposit_nagad_account', $defaultSetting->deposit_nagad_account) }}" placeholder="Deposit Nagad Account">
                            @error('deposit_nagad_account')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="min_deposit_amount" class="form-label">Minimum Deposit Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="min_deposit_amount" name="min_deposit_amount" value="{{ old('min_deposit_amount', $defaultSetting->min_deposit_amount) }}" placeholder="Minimum Deposit Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('min_deposit_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="max_deposit_amount" class="form-label">Maximum Deposit Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_deposit_amount" name="max_deposit_amount" value="{{ old('max_deposit_amount', $defaultSetting->max_deposit_amount) }}" placeholder="Maximum Deposit Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('max_deposit_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="deposit_from_withdraw_balance_charge_percentage" class="form-label">Deposit From Withdraw Balance Charge Percentage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="deposit_from_withdraw_balance_charge_percentage" name="deposit_from_withdraw_balance_charge_percentage" value="{{ old('deposit_from_withdraw_balance_charge_percentage', $defaultSetting->deposit_from_withdraw_balance_charge_percentage) }}" placeholder="Deposit From Withdraw Balance Charge Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('deposit_from_withdraw_balance_charge_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="withdraw_balance_from_deposit_balance_charge_percentage" class="form-label">Withdraw Balance From Deposit Balance Charge Percentage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="withdraw_balance_from_deposit_balance_charge_percentage" name="withdraw_balance_from_deposit_balance_charge_percentage" value="{{ old('withdraw_balance_from_deposit_balance_charge_percentage', $defaultSetting->withdraw_balance_from_deposit_balance_charge_percentage) }}" placeholder="Withdraw Balance From Deposit Balance Charge Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('withdraw_balance_from_deposit_balance_charge_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="instant_withdraw_charge" class="form-label">Instant Withdraw Charge <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="instant_withdraw_charge" name="instant_withdraw_charge" value="{{ old('instant_withdraw_charge', $defaultSetting->instant_withdraw_charge) }}" placeholder="Instant Withdraw Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('instant_withdraw_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="withdraw_charge_percentage" class="form-label">Withdraw Charge Percentage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="withdraw_charge_percentage" name="withdraw_charge_percentage" value="{{ old('withdraw_charge_percentage', $defaultSetting->withdraw_charge_percentage) }}" placeholder="Withdraw Charge Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('withdraw_charge_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="min_withdraw_amount" class="form-label">Minimum Withdraw Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="min_withdraw_amount" name="min_withdraw_amount" value="{{ old('min_withdraw_amount', $defaultSetting->min_withdraw_amount) }}" placeholder="Minimum Withdraw Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('min_withdraw_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="max_withdraw_amount" class="form-label">Maximum Withdraw Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_withdraw_amount" name="max_withdraw_amount" value="{{ old('max_withdraw_amount', $defaultSetting->max_withdraw_amount) }}" placeholder="Maximum Withdraw Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('max_withdraw_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="task_posting_charge_percentage" class="form-label">Task Posting Charge Percentage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="task_posting_charge_percentage" name="task_posting_charge_percentage" value="{{ old('task_posting_charge_percentage', $defaultSetting->task_posting_charge_percentage) }}" placeholder="Task Posting Charge Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('task_posting_charge_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="task_posting_additional_required_proof_photo_charge" class="form-label">Task Posting Additional Required Proof Photo Charge <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="task_posting_additional_required_proof_photo_charge" name="task_posting_additional_required_proof_photo_charge" value="{{ old('task_posting_additional_required_proof_photo_charge', $defaultSetting->task_posting_additional_required_proof_photo_charge) }}" placeholder="Task Posting Additional Required Proof Photo Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            <small class="text-info">* Every additional required proof photo charge</small>
                            @error('task_posting_additional_required_proof_photo_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="task_posting_boosting_time_charge" class="form-label">Task Posting Boosting Time Charge <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="task_posting_boosting_time_charge" name="task_posting_boosting_time_charge" value="{{ old('task_posting_boosting_time_charge', $defaultSetting->task_posting_boosting_time_charge) }}" placeholder="Task Posting Boosting Time Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            <small class="text-info">* Every 15 minutes charge</small>
                            @error('task_posting_boosting_timeee_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="task_posting_additional_work_duration_charge" class="form-label">Task Posting Additional Work Duration Charge <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="task_posting_additional_work_duration_charge" name="task_posting_additional_work_duration_charge" value="{{ old('task_posting_additional_work_duration_charge', $defaultSetting->task_posting_additional_work_duration_charge) }}" placeholder="Task Posting Additional Work Duration Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            <small class="text-info">* Every additional Work Duration charge</small>
                            @error('task_posting_additional_work_duration_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="task_posting_min_budget" class="form-label">Task Posting Min Budget <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="task_posting_min_budget" name="task_posting_min_budget" value="{{ old('task_posting_min_budget', $defaultSetting->task_posting_min_budget) }}" placeholder="Task Posting Min Budget">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('task_posting_min_budget')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="posted_task_proof_submit_user_max_bonus_amount" class="form-label">Posted Task Proof Submit User Max Bonus Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="posted_task_proof_submit_user_max_bonus_amount" name="posted_task_proof_submit_user_max_bonus_amount" value="{{ old('posted_task_proof_submit_user_max_bonus_amount', $defaultSetting->posted_task_proof_submit_user_max_bonus_amount) }}" placeholder="Posted Task Proof Submit User Max Bonus Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('posted_task_proof_submit_user_max_bonus_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="posted_task_proof_submit_auto_approved_time" class="form-label">Posted Task Proof Submit Auto Approved Time <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="posted_task_proof_submit_auto_approved_time" name="posted_task_proof_submit_auto_approved_time" value="{{ old('posted_task_proof_submit_auto_approved_time', $defaultSetting->posted_task_proof_submit_auto_approved_time) }}" placeholder="Posted Task Proof Submit Auto Approved Time">
                            @error('posted_task_proof_submit_auto_approved_time')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="posted_task_proof_submit_rejected_charge_auto_refund_time" class="form-label">Posted Task Proof Submit Rejected Charge Auto Refund Time <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="posted_task_proof_submit_rejected_charge_auto_refund_time" name="posted_task_proof_submit_rejected_charge_auto_refund_time" value="{{ old('posted_task_proof_submit_rejected_charge_auto_refund_time', $defaultSetting->posted_task_proof_submit_rejected_charge_auto_refund_time) }}" placeholder="Posted Task Proof Submit Rejected Charge Auto Refund Time">
                            @error('posted_task_proof_submit_rejected_charge_auto_refund_time')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="rejected_worked_task_review_charge" class="form-label">Rejected Worked Task Review Charge <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="rejected_worked_task_review_charge" name="rejected_worked_task_review_charge" value="{{ old('rejected_worked_task_review_charge', $defaultSetting->rejected_worked_task_review_charge) }}" placeholder="Rejected Worked Task Review Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('rejected_worked_task_review_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="user_max_blocked_time_for_banned" class="form-label">User Max Blocked Time For Banned <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="user_max_blocked_time_for_banned" name="user_max_blocked_time_for_banned" value="{{ old('user_max_blocked_time_for_banned', $defaultSetting->user_max_blocked_time_for_banned) }}" placeholder="User Max Blocked Time For Banned">
                            @error('user_max_blocked_time_for_banned')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="user_blocked_instant_resolved_charge" class="form-label">User Blocked Instant Resolved Charge <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="user_blocked_instant_resolved_charge" name="user_blocked_instant_resolved_charge" value="{{ old('user_blocked_instant_resolved_charge', $defaultSetting->user_blocked_instant_resolved_charge) }}" placeholder="User Blocked Instant Resolved Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('user_blocked_instant_resolved_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                    </div><!-- Row -->
                    <div class="row mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function(){

    })
</script>
@endsection

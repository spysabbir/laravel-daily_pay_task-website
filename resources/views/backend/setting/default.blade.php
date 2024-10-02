@extends('layouts.template_master')

@section('title', 'Default Setting')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Default Setting</h3>
            </div>
            <div class="card-body">
                <form class="forms-sample" action="{{ route('backend.default.setting.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="referral_registration_bonus_amount" class="form-label">Referral Registration Bonus Amount</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="referral_registration_bonus_amount" name="referral_registration_bonus_amount" value="{{ old('referral_registration_bonus_amount', $defaultSetting->referral_registration_bonus_amount) }}" placeholder="Referral Registration Bonus Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('referral_registration_bonus_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="referral_withdrawal_bonus_percentage" class="form-label">Referral Withdrawal Bonus Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="referral_withdrawal_bonus_percentage" name="referral_withdrawal_bonus_percentage" value="{{ old('referral_withdrawal_bonus_percentage', $defaultSetting->referral_withdrawal_bonus_percentage) }}" placeholder="Referral Withdrawal Bonus Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('referral_withdrawal_bonus_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="deposit_bkash_account" class="form-label">Deposit Bkash Account</label>
                            <input type="number" class="form-control" id="deposit_bkash_account" name="deposit_bkash_account" value="{{ old('deposit_bkash_account', $defaultSetting->deposit_bkash_account) }}" placeholder="Deposit Bkash Account">
                            @error('deposit_bkash_account')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="deposit_rocket_account" class="form-label">Deposit Rocket Account</label>
                            <input type="number" class="form-control" id="deposit_rocket_account" name="deposit_rocket_account" value="{{ old('deposit_rocket_account', $defaultSetting->deposit_rocket_account) }}" placeholder="Deposit Rocket Account">
                            @error('deposit_rocket_account')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="deposit_nagad_account" class="form-label">Deposit Nagad Account</label>
                            <input type="number" class="form-control" id="deposit_nagad_account" name="deposit_nagad_account" value="{{ old('deposit_nagad_account', $defaultSetting->deposit_nagad_account) }}" placeholder="Deposit Nagad Account">
                            @error('deposit_nagad_account')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="min_deposit_amount" class="form-label">Minimum Deposit Amount</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="min_deposit_amount" name="min_deposit_amount" value="{{ old('min_deposit_amount', $defaultSetting->min_deposit_amount) }}" placeholder="Minimum Deposit Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('min_deposit_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="max_deposit_amount" class="form-label">Maximum Deposit Amount</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_deposit_amount" name="max_deposit_amount" value="{{ old('max_deposit_amount', $defaultSetting->max_deposit_amount) }}" placeholder="Maximum Deposit Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('max_deposit_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="withdrawal_balance_deposit_charge_percentage" class="form-label">Withdrawal Balance Deposit Charge Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="withdrawal_balance_deposit_charge_percentage" name="withdrawal_balance_deposit_charge_percentage" value="{{ old('withdrawal_balance_deposit_charge_percentage', $defaultSetting->withdrawal_balance_deposit_charge_percentage) }}" placeholder="Withdrawal Balance Deposit Charge Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('withdrawal_balance_deposit_charge_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="instant_withdraw_charge" class="form-label">Instant Withdraw Charge</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="instant_withdraw_charge" name="instant_withdraw_charge" value="{{ old('instant_withdraw_charge', $defaultSetting->instant_withdraw_charge) }}" placeholder="Instant Withdraw Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('instant_withdraw_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="withdraw_charge_percentage" class="form-label">Withdraw Charge Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="withdraw_charge_percentage" name="withdraw_charge_percentage" value="{{ old('withdraw_charge_percentage', $defaultSetting->withdraw_charge_percentage) }}" placeholder="Withdraw Charge Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('withdraw_charge_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="min_withdraw_amount" class="form-label">Minimum Withdraw Amount</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="min_withdraw_amount" name="min_withdraw_amount" value="{{ old('min_withdraw_amount', $defaultSetting->min_withdraw_amount) }}" placeholder="Minimum Withdraw Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('min_withdraw_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="max_withdraw_amount" class="form-label">Maximum Withdraw Amount</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_withdraw_amount" name="max_withdraw_amount" value="{{ old('max_withdraw_amount', $defaultSetting->max_withdraw_amount) }}" placeholder="Maximum Withdraw Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('max_withdraw_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="job_posting_charge_percentage" class="form-label">Job Posting Charge Percentage</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="job_posting_charge_percentage" name="job_posting_charge_percentage" value="{{ old('job_posting_charge_percentage', $defaultSetting->job_posting_charge_percentage) }}" placeholder="Job Posting Charge Percentage">
                                <span class="input-group-text input-group-addon">%</span>
                            </div>
                            @error('job_posting_charge_percentage')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="job_posting_additional_screenshot_charge" class="form-label">Job Posting Additional Screenshot Charge</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="job_posting_additional_screenshot_charge" name="job_posting_additional_screenshot_charge" value="{{ old('job_posting_additional_screenshot_charge', $defaultSetting->job_posting_additional_screenshot_charge) }}" placeholder="Job Posting Additional Screenshot Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            <small class="text-info">* Every additional screenshot charge</small>
                            @error('job_posting_additional_screenshot_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="job_posting_boosted_time_charge" class="form-label">Job Posting Boosted Time Charge</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="job_posting_boosted_time_charge" name="job_posting_boosted_time_charge" value="{{ old('job_posting_boosted_time_charge', $defaultSetting->job_posting_boosted_time_charge) }}" placeholder="Job Posting Boosted Time Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            <small class="text-info">* Every 15 minutes charge</small>
                            @error('job_posting_boosted_time_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="job_posting_additional_running_day_charge" class="form-label">Job Posting Additional Running Day Charge</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="job_posting_additional_running_day_charge" name="job_posting_additional_running_day_charge" value="{{ old('job_posting_additional_running_day_charge', $defaultSetting->job_posting_additional_running_day_charge) }}" placeholder="Job Posting Additional Running Day Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            <small class="text-info">* Every additional day charge</small>
                            @error('job_posting_additional_running_day_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="job_posting_min_budget" class="form-label">Job Posting Min Budget</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="job_posting_min_budget" name="job_posting_min_budget" value="{{ old('job_posting_min_budget', $defaultSetting->job_posting_min_budget) }}" placeholder="Job Posting Min Budget">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('job_posting_min_budget')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="max_job_proof_bonus_amount" class="form-label">Max Job Proof Bonus Amount</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_job_proof_bonus_amount" name="max_job_proof_bonus_amount" value="{{ old('max_job_proof_bonus_amount', $defaultSetting->max_job_proof_bonus_amount) }}" placeholder="Max Job Proof Bonus Amount">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('max_job_proof_bonus_amount')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="job_proof_monthly_free_review_time" class="form-label">Job Proof Monthly Free Review Time</label>
                            <input type="number" class="form-control" id="job_proof_monthly_free_review_time" name="job_proof_monthly_free_review_time" value="{{ old('job_proof_monthly_free_review_time', $defaultSetting->job_proof_monthly_free_review_time) }}" placeholder="Job Proof Monthly Free Review Time">
                            @error('job_proof_monthly_free_review_time')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="job_proof_additional_review_charge" class="form-label">Job Proof Additional Review Charge</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="job_proof_additional_review_charge" name="job_proof_additional_review_charge" value="{{ old('job_proof_additional_review_charge', $defaultSetting->job_proof_additional_review_charge) }}" placeholder="Job Proof Additional Review Charge">
                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                            </div>
                            @error('job_proof_additional_review_charge')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="user_max_blocked_time" class="form-label">User Max Blocked Time</label>
                            <input type="number" class="form-control" id="user_max_blocked_time" name="user_max_blocked_time" value="{{ old('user_max_blocked_time', $defaultSetting->user_max_blocked_time) }}" placeholder="User Max Blocked Time">
                            @error('user_max_blocked_time')
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

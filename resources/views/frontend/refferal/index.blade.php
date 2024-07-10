@extends('layouts.template_master')

@section('title', 'Referral')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Referral</h3>
            </div>
            <div class="card-body">
                <div  class="mb-3">
                    <label for="referral-link" class="form-label">Referral Link</label>
                    <div class="d-flex">
                        <input class="form-control" type="text" id="referral-link" value="{{ Auth::user()->generateReferralLink() }}" readonly>
                        <button onclick="copyReferralLink()" class="btn btn-info">
                            <i class="link-icon" data-feather="copy"></i>
                        </button>
                    </div>
                </div>
                <div class="mt-3 bg-dark p-3 rounded">
                    <h5 class="text-info mb-3">
                        {{ App\Models\User::where('referred_by', Auth::user()->id)->count() }} friends joined using your referral link.
                    </h5>
                    <strong class="text-success">
                        {{ get_default_settings('site_referal_registion_bonus_amount') * App\Models\User::where('referred_by', Auth::user()->id)->count() }} {{ get_default_settings('site_currency') }} earned from your refferal link.
                    </strong>
                    <p class="mt-3">
                        Share your referral link with your friends and earn {{ get_default_settings('site_referal_registion_bonus_amount') }} {{ get_default_settings('site_currency') }} for each friend that signs up using your link.
                    </p>
                    <p class="mt-3">
                        If your friends sign up using your referral link and work on our platform, you will earn {{ get_default_settings('site_referal_earning_bonus_percentage') }}% of their earnings.
                    </p>
                    <p class="mt-3">
                        Your friends will also get {{ get_default_settings('site_referal_registion_bonus_amount') }} {{ get_default_settings('site_currency') }} when they sign up using your referral link.
                    </p>
                    <p class="mt-3">
                        You can also share your referral link on social media platforms like Facebook, Twitter, WhatsApp, etc.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function copyReferralLink() {
        var copyText = document.getElementById("referral-link");

        copyText.select();
        copyText.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(copyText.value).then(function() {
            toastr.success('Referral link copied to clipboard!');
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endsection

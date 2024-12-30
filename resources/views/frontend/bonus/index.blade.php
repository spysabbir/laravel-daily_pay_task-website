@extends('layouts.template_master')

@section('title', 'Bonus')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Bonus List</h3>
                    <p class="mb-0">
                        You and new user will get {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('referral_registration_bonus_amount') }} taka bonus when new user account verification is completed by your referral link. If the user sign up by your referral link then you will get {{ get_default_settings('referral_withdrawal_bonus_percentage') }} % bonus of their every withdrawal amount. The buyer can give you a bonus if your work proof is satisfied so work with honesty and perfectly. You will get more bonus in the future so work on our website and share your referral link in others. Contact us if you face any problems.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading text-center">
                            <i class="link-icon" data-feather="credit-card"></i>
                            Total Bonus: {{ get_site_settings('site_currency_symbol') }} {{ $total_bonus }}
                        </h4>
                    </div>
                </div>
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_type" class="form-label">Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Type --</option>
                                    <option value="Referral Registration Bonus">Referral Registration Bonus</option>
                                    <option value="Referral Withdrawal Bonus">Referral Withdrawal Bonus</option>
                                    <option value="Proof Task Approved Bonus">Proof Task Approved Bonus</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Type</th>
                                <th>By</th>
                                <th>For User</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('bonus') }}",
                data: function (e) {
                    e.type = $('#filter_type').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'type', name: 'type' },
                { data: 'bonus_by', name: 'bonus_by' },
                { data: 'for_user', name: 'for_user' },
                { data: 'amount', name: 'amount' },
                { data: 'created_at', name: 'created_at' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

    });
</script>
@endsection

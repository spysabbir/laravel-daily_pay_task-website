@extends('layouts.template_master')

@section('title', 'Bonus')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Bonus List</h3>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_type">Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Type --</option>
                                    <option value="Referral Registration Bonus">Referral Registration Bonus</option>
                                    <option value="Referral Withdrawal Bonus">Referral Withdrawal Bonus</option>
                                    <option value="Job Proof Approved Bonus">Job Proof Approved Bonus</option>
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
                                <th>Bonus Type</th>
                                <th>Bonus By</th>
                                <th>Amount</th>
                                <th>Created At</th>
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

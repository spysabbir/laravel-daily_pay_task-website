@extends('layouts.template_master')

@section('title', 'Favorite User List')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Favorite User List</h3>
                    <h3>Total: <span id="total_favorites_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi user, This is the favorite user list so when you favorite buyers you can see the favorite buyers here. You can favorite or unfavorite buyers at any time. When you favorite the buyer you will see the list of tasks posted by the buyer at the top but if you unfavorite then again you will see the list of tasks posted by the buyer in general. Please contact us if you face any problems.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Favorite User</th>
                                <th>Favorite At</th>
                                <th>Action</th>
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
                url: "{{ route('favorite.user.list') }}",
                dataSrc: function (json) {
                    // Update total favorite count
                    $('#total_favorites_count').text(json.totalFavoritesCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'favorite_user', name: 'favorite_user' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' }
            ]
        });
    });
</script>
@endsection

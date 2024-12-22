@extends('layouts.template_master')

@section('title', 'Find Tasks')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Find Tasks</h3>
                    <h3>Total: <span id="total_tasks_count">0</span></h3>
                </div>
                <p class="card-description text-info">
                    Note: Hi everyone, find the preferred task from the below list to working and will get result from buyers within 72 hours after submitting task proof. You will get automatically payment if the buyer does not approve or reject your Task Proof within 72 hours so work with honesty and perfectly. Alert: Please don’t' try to hassle others and submit false work. Please contact us if you face any problem, Thanks.
                </p>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-xl-3 col-lg-5 mb-3">
                            <div class="form-group">
                                <label for="filter_category_id" class="form-label">Category</label>
                                <select class="form-select filter_data" id="filter_category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->category->id }}">{{ $category->category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-5 mb-3">
                            <div class="form-group">
                                <label for="filter_sort_by" class="form-label">Sort By</label>
                                <select class="form-select filter_data" id="filter_sort_by">
                                    <option value="">-- Select Sort By --</option>
                                    <option value="latest">Latest</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="low_to_high">Low to High</option>
                                    <option value="high_to_low">High to Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-2 mb-3">
                            <div class="form-group mt-1">
                                <button class="btn btn-danger btn-block mt-4" id="clear_filters">Clear Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Category</th>
                                <th>Task Title</th>
                                <th>Worker Earn</th>
                                <th>Proof Submitted</th>
                                <th>Approved At</th>
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

        // Store filters in localStorage
        function storeFilters() {
            localStorage.setItem('filter_category_id', $('#filter_category_id').val());
            localStorage.setItem('filter_sort_by', $('#filter_sort_by').val());
        }

        // Restore filters from localStorage
        function restoreFilters() {
            if (localStorage.getItem('filter_category_id')) {
                $('#filter_category_id').val(localStorage.getItem('filter_category_id'));
            }
            if (localStorage.getItem('filter_sort_by')) {
                $('#filter_sort_by').val(localStorage.getItem('filter_sort_by'));
            }
        }

        // Clear filters
        function clearFilters() {
            localStorage.removeItem('filter_category_id');
            localStorage.removeItem('filter_sort_by');
            $('#filter_category_id').val('');
            $('#filter_sort_by').val('');
        }

        // Check if filters should be cleared
        @if ($clearFilters)
            clearFilters();
        @else
            restoreFilters();
        @endif

        // Initialize DataTable
        var table = $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('find_tasks') }}",
                data: function (d) {
                    d.category_id = $('#filter_category_id').val();
                    d.sort_by = $('#filter_sort_by').val();
                },
                dataSrc: function (json) {
                    // Update total task count
                    $('#total_tasks_count').text(json.totalTasksCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'category_name', name: 'category_name' },
                { data: 'title', name: 'title' },
                { data: 'income_of_each_worker', name: 'income_of_each_worker' },
                { data: 'worker_needed', name: 'worker_needed' },
                { data: 'approved_at', name: 'approved_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Reload table when filter values change
        $('.filter_data').change(function() {
            storeFilters();
            table.ajax.reload();
        });

        // Clear filters manually when the clear button is clicked
        $('#clear_filters').on('click', function() {
            clearFilters();
            table.ajax.reload();
        });
    });
</script>
@endsection

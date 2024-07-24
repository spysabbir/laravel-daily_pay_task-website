@extends('layouts.template_master')

@section('title', 'Post Job')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Post Job</h4>
                <form id="jobForm" action="{{ route('post_job.submit') }}" method="post">
                    @csrf
                    <div id="wizard" class="border">
                        <h2>Notice</h2>
                        <section>
                            <h4 class="mb-3">First Step</h4>
                            <p>
                                Donec mi sapien, hendrerit nec egestas a, rutrum vitae dolor. Nullam venenatis diam ac ligula elementum pellentesque.
                            </p>
                        </section>

                        <h2>Select Category</h2>
                        <section>
                            <div class="mb-3">
                                <label class="form-label">Select Category</label>
                                <div id="category-options">
                                    @foreach($categories as $category)
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" name="category_id" id="category_{{ $category->id }}" value="{{ $category->id }}" required>
                                        <label class="form-check-label" for="category_{{ $category->id }}">{{ $category->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>
                            <div class="mb-3" id="sub-category-section" style="display:none;">
                                <label class="form-label">Select Sub Category</label>
                                <div id="sub-category-options">
                                    <!-- Sub-category radio buttons will be loaded here -->
                                </div>
                                <div class="invalid-feedback">Please select a sub category.</div>
                            </div>
                            <div class="mb-3" id="child-category-section" style="display:none;">
                                <label class="form-label">Select Child Category</label>
                                <div id="child-category-options">
                                    <!-- Child-category radio buttons will be loaded here -->
                                </div>
                                <div class="invalid-feedback">Please select a child category.</div>
                            </div>
                        </section>

                        <h2>Job Information</h2>
                        <section>
                            <div class="mb-3">
                                <label for="title" class="form-label">Job Title</label>
                                <input type="text" class="form-control" name="title" id="title" required>
                                <div class="invalid-feedback">Please enter a title.</div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Job Description</label>
                                <textarea class="form-control" name="description" id="description" required></textarea>
                                <div class="invalid-feedback">Please enter a description.</div>
                            </div>
                            <div class="mb-3">
                                <label for="requirements" class="form-label">Job Requirements</label>
                                <textarea class="form-control" name="requirements" id="requirements" required></textarea>
                                <div class="invalid-feedback">Please enter requirements.</div>
                            </div>
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail</label>
                                <input type="file" class="form-control" name="thumbnail" id="thumbnail" required>
                                <div class="invalid-feedback">Please select a thumbnail.</div>
                            </div>
                        </section>

                        <h2>Charge & Setting</h2>
                        <section>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="need_worker" class="form-label">Workers are needed</label>
                                        <input type="number" class="form-control" name="need_worker" id="need_worker" value="1" required>
                                        <div class="invalid-feedback">Please enter how many workers are required.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="worker_charge" class="form-label">Each worker charge</label>
                                        <input type="number" class="form-control" name="worker_charge" id="worker_charge" required>
                                        <div class="invalid-feedback">Please enter the charges for each worker.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="extra_screenshots" class="form-label">Additional screenshots are required</label>
                                        <input type="number" class="form-control" name="extra_screenshots" id="extra_screenshots" value="0" required>
                                        <div class="invalid-feedback">Please enter how many additional screenshots are required</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="job_running_day" class="form-label">Job Running Day</label>
                                        <select class="form-select" name="job_running_day" id="job_running_day" required>
                                            <option value="3" selected>3 Days</option>
                                            <option value="4">4 Days</option>
                                            <option value="5">5 Days</option>
                                            <option value="6">6 Days</option>
                                            <option value="7">1 Week</option>
                                            <option value="14">2 Weeks</option>
                                            <option value="21">3 Weeks</option>
                                            <option value="30">1 Month</option>
                                        </select>
                                        <div class="invalid-feedback">Please enter the job running day.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="job_boosted_time" class="form-label">Job Boosted time</label>
                                        <select class="form-select" name="job_boosted_time" id="job_boosted_time" required>
                                            <option value="0" selected>No Boost</option>
                                            <option value="15">15 Manutes</option>
                                            <option value="30">30 Manutes</option>
                                            <option value="45">45 Manutes</option>
                                            <option value="60">1 Hour</option>
                                            <option value="120">2 Hours</option>
                                            <option value="180">3 Hours</option>
                                            <option value="240">4 Hours</option>
                                            <option value="300">5 Hours</option>
                                            <option value="360">6 Hours</option>
                                        </select>
                                        <div class="invalid-feedback">Please enter the job boosted time.</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="total_job_cost" class="form-label">Total Job Cost</label>
                                        <input type="number" class="form-control" id="total_job_cost" disabled>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
    $('#wizard').steps({
        headerTag: 'h2',
        bodyTag: 'section',
        transitionEffect: 'slideLeft',
        autoFocus: true,
        onStepChanging: function(event, currentIndex, newIndex) {
            if (newIndex < currentIndex) {
                return true;
            }

            var form = $('#jobForm');
            var isValid = true;

            if (currentIndex === 1 ) {
                var categorySelected = $('input[name="category_id"]:checked').val();
                if (!categorySelected) {
                    $('#category-options').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#category-options').removeClass('is-invalid');
                }

                if (categorySelected) {
                    var subCategorySelected = $('input[name="sub_category_id"]:checked').val();
                    if (!subCategorySelected) {
                        $('#sub-category-options').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#sub-category-options').removeClass('is-invalid');
                    }
                }

                if (subCategorySelected) {
                    var childCategorySelected = $('input[name="child_category_id"]:checked').val();
                    if (!childCategorySelected) {
                        $('#child-category-options').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#child-category-options').removeClass('is-invalid');
                    }
                }

                return isValid;
            }

            form.find('section').eq(currentIndex).find(':input[required]').each(function() {
                if (!this.checkValidity()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            return isValid;
        },
        onFinishing: function(event, currentIndex) {
            var form = $('#jobForm');
            var isValid = true;

            // Validate all required inputs
            form.find(':input[required]').each(function() {
                if (!this.checkValidity()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            return isValid;
        },
        onFinished: function(event, currentIndex) {
            $('#jobForm').submit();
        }
    });

    // Load sub-categories when a category is selected
    $('input[name="category_id"]').change(function() {
        var category_id = $(this).val();
        $.ajax({
            url: "{{ route('post_job.get_sub_category') }}",
            type: 'GET',
            data: { category_id: category_id },
            success: function(response) {
                $('#sub-category-section').show();
                $('#sub-category-options').html(response.html);
                $('#child-category-section').hide();
                $('#child-category-options').html('');
                $('#work_charge').val('');
            }
        });
    });

    // Load child categories when a sub-category is selected
    $(document).on('change', 'input[name="sub_category_id"]', function() {
        var sub_category_id = $(this).val();
        var category_id = $('input[name="category_id"]:checked').val();
        $.ajax({
            url: "{{ route('post_job.get_child_category') }}",
            type: 'GET',
            data: { category_id: category_id, sub_category_id: sub_category_id },
            success: function(response) {
                if (response.child_categories && response.child_categories.length > 0) {
                    var options = '';
                    $.each(response.child_categories, function(index, child_category) {
                        options += '<div class="form-check form-check-inline">';
                        options += '<input type="radio" class="form-check-input" name="child_category_id" id="child_category_' + child_category.id + '" value="' + child_category.id + '">';
                        options += '<label class="form-check-label" for="child_category_' + child_category.id + '">' + child_category.name + '</label>';
                        options += '</div>';
                    });
                    $('#child-category-options').html(options);
                    $('#child-category-section').show();
                } else {
                    $('#child-category-section').hide();
                    $('#child-category-options').html('');
                }
                $('#worker_charge').val(response.working_charges.working_min_charges);
            }
        });
    });

    // Update work charge based on selected child category
    $(document).on('change', 'input[name="child_category_id"]', function() {
        var category_id = $('input[name="category_id"]:checked').val();
        var sub_category_id = $('input[name="sub_category_id"]:checked').val();
        var child_category_id = $(this).val();
        if (child_category_id) {
            $.ajax({
                url: "{{ route('post_job.get_job_charge') }}",
                type: 'GET',
                data: {
                    category_id: category_id,
                    sub_category_id: sub_category_id,
                    child_category_id: child_category_id
                },
                success: function(response) {
                    $('#worker_charge').val(response.working_charges.working_min_charges);
                }
            });
        }
    });
});
</script>
@endsection

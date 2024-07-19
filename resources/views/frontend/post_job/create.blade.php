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
                        <h2>First Step</h2>
                        <section>
                            <h4 class="mb-3">First Step</h4>
                            <p>
                                Donec mi sapien, hendrerit nec egestas a, rutrum vitae dolor. Nullam venenatis diam ac ligula elementum pellentesque.
                            </p>
                        </section>

                        <h2>Select Category</h2>
                        <section>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" name="category_id" id="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>
                            <div class="mb-3">
                                <label for="sub_category_id" class="form-label">Sub Category</label>
                                <select class="form-select" name="sub_category_id" id="sub_category_id" required>
                                    <option value="">Select Sub Category</option>
                                </select>
                                <div class="invalid-feedback">Please select a sub category.</div>
                            </div>
                            <div class="mb-3" id="child-category-section" style="display:none;">
                                <label for="child_category_id" class="form-label">Child Category</label>
                                <select class="form-select" name="child_category_id" id="child_category_id">
                                    <option value="">Select Child Category</option>
                                </select>
                                <div class="invalid-feedback">Please select a child category.</div>
                            </div>
                        </section>

                        <h2>Job Information</h2>
                        <section>
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" id="title" required>
                                <div class="invalid-feedback">Please enter a title.</div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" required></textarea>
                                <div class="invalid-feedback">Please enter a description.</div>
                            </div>
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail</label>
                                <input type="file" class="form-control" name="thumbnail" id="thumbnail" required>
                                <div class="invalid-feedback">Please select a thumbnail.</div>
                            </div>
                        </section>

                        <h2>Charge & Setting</h2>
                        <section>
                            <div class="mb-3">
                                <label for="worker_need" class="form-label">Worker Need</label>
                                <input type="number" class="form-control" name="worker_need" id="worker_need" required>
                                <div class="invalid-feedback">Please enter a worker need.</div>
                            </div>
                            <div class="mb-3">
                                <label for="work_charge" class="form-label">Work Charge</label>
                                <input type="number" class="form-control" name="work_charge" id="work_charge" required>
                                <div class="invalid-feedback">Please enter a work charge.</div>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_time" class="form-label">Delivery Time</label>
                                <input type="number" class="form-control" name="delivery_time" id="delivery_time" required>
                                <div class="invalid-feedback">Please enter a delivery time.</div>
                            </div>
                            <div class="mb-3">
                                <label for="extra_screenshots_require" class="form-label">Extra Screenshots Require</label>
                                <input type="number" class="form-control" name="extra_screenshots_require" id="extra_screenshots_require" required>
                                <div class="invalid-feedback">Please enter extra screenshots required.</div>
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
                    return true; // allow moving back to previous steps without validation
                }
                var form = $('#jobForm');
                var isValid = true;

                // Validate current step
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

                // Validate all steps before finishing
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

        $('#category_id').change(function() {
            var category_id = $(this).val();
            $.ajax({
                url: "{{ route('post_job.get_sub_category') }}",
                type: 'GET',
                data: {
                    category_id: category_id
                },
                success: function(response) {
                    $('#sub_category_id').html(response);
                    $('#child-category-section').hide();
                    $('#child_category_id').html('<option value="">Select Child Category</option>');
                    $('#work_charge').val(''); // Reset work charge
                }
            });
        });

        $('#sub_category_id').change(function() {
            var sub_category_id = $(this).val();
            $.ajax({
                url: "{{ route('post_job.get_child_category') }}",
                type: 'GET',
                data: {
                    sub_category_id: sub_category_id
                },
                success: function(response) {
                    if (response.child_categories && response.child_categories.length > 0) {
                        var options = '<option value="">Select Child Category</option>';
                        $.each(response.child_categories, function(index, child_category) {
                            options += '<option value="' + child_category.id + '">' + child_category.name + '</option>';
                        });
                        $('#child_category_id').html(options);
                        $('#child-category-section').show();
                    } else {
                        $('#child-category-section').hide();
                        $('#child_category_id').html('<option value="">Select Child Category</option>');
                    }
                    $('#work_charge').val(response.working_min_charges);
                }
            });
        });

        $('#child_category_id').change(function() {
            var category_id = $('#category_id').val();
            var sub_category_id = $('#sub_category_id').val();
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
                        $('#work_charge').val(response.working_min_charges);
                    }
                });
            } else {
                $.ajax({
                    url: "{{ route('post_job.get_job_charge') }}",
                    type: 'GET',
                    data: {
                        category_id: category_id,
                        sub_category_id: sub_category_id
                    },
                    success: function(response) {
                        $('#work_charge').val(response.working_min_charges);
                    }
                });
            }
        });
    });
</script>
@endsection

@extends('layouts.template_master')

@section('title', 'Edit Task')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Task</h4>
                {{-- Error Showing --}}
                @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input. <br> <br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                {{-- Error Showing --}}
                <form id="taskForm" action="{{ route('post_task.update', $postTask->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div id="wizard">
                        <!-- Notice Section -->
                        <h2>Notice</h2>
                        <section>
                            @if ($postTask->rejection_reason)
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <strong>Warning!</strong> This task post has been rejected. <br>
                                <span>Rejected At: {{ date('d-m-Y h:i:s A', strtotime($postTask->rejected_at)) }}</span> <br>
                                <span>Rejection Reason: {{ $postTask->rejection_reason }}</span> <br>
                            </div>
                            @endif
                            <h4 class="mb-3">
                                <strong>Important Notice:</strong>
                            </h4>
                            <div class="mb-3">
                                <strong>1. </strong> Please fill out the form carefully. Once you submit the form, you can't edit it. <br>
                                <strong>2. </strong> You can't cancel the task once you submit it. <br>
                                <strong>3. </strong> You can't get a refund once you submit the task. <br>
                                <strong>4. </strong> You can't get a refund if you don't complete the task. <br>
                                <strong>5. </strong> Your task will be boosted for the selected time. <br>
                                <strong>6. </strong> Your task will be running for the selected days. <br>
                                <strong>7. </strong> You can't change the task category once you submit the task. <br>
                                <strong>8. </strong> You can only change the task Charge if you want to increase it. <br>
                                <strong>9. </strong> You can't change the task Charge if you want to decrease it. <br>
                                <strong>10. </strong> You can't change the task thumbnail once you submit the task. <br>
                                <strong>11. </strong> You can only change the task work needed if you want to increase it. <br>
                                <strong>12. </strong> You can't change the task work needed if you want to decrease it. <br>
                                <strong>13. </strong> Youy task is not aproved if you provide wrong information. <br>
                                <strong>14. </strong> You task is not submit if your total task Charge is not 100. <br>
                            </div>
                        </section>

                        <!-- Category Section -->
                        <h2>Select Category</h2>
                        <section>
                            <div class="mb-3 border p-2">
                                <h5 class="bg-dark text-center py-1 mb-3 rounded">
                                    Select Category <small class="text-danger">* Required</small>
                                </h5>
                                <div id="category-options">
                                    @foreach($categories as $category)
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" name="category_id" id="category_{{ $category->id }}" value="{{ $category->id }}" {{ $postTask->category_id == $category->id ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            <span class="badge bg-primary">{{ $category->name }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>
                            <div class="mb-3 border p-2" id="sub-category-section" style="display:none;">
                                <h5 class="bg-dark text-center py-1 mb-3 rounded">
                                    Select Sub Category <small class="text-danger">* Required</small>
                                </h5>
                                <div id="sub-category-options">
                                    <!-- Sub-category radio buttons will be loaded here -->
                                </div>
                                <div class="invalid-feedback">Please select a sub category.</div>
                            </div>
                            <div class="mb-3 border p-2" id="child-category-section" style="display:none;">
                                <h5 class="bg-dark text-center py-1 mb-3 rounded">
                                    Select Child Category <small class="text-danger">* Required</small>
                                </h5>
                                <div id="child-category-options">
                                    <!-- Child-category radio buttons will be loaded here -->
                                </div>
                                <div class="invalid-feedback">Please select a child category.</div>
                            </div>
                        </section>

                        <!-- Task Information Section -->
                        <h2>Task Information</h2>
                        <section>
                            <div class="mb-2">
                                <label for="title" class="form-label">
                                    Title <small class="text-danger">* Required</small>
                                </label>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Please enter a title." value="{{ old('title', $postTask->title) }}" required>
                                <div class="invalid-feedback">Please enter a title.</div>
                            </div>
                            <div class="mb-2">
                                <label for="description" class="form-label">
                                    Description <small class="text-danger">* Required</small>
                                </label>
                                <textarea class="form-control" name="description" id="description" rows="4" placeholder="Please enter a description." required>{{ old('description', $postTask->description) }}</textarea>
                                <div class="invalid-feedback">Please enter a description.</div>
                            </div>
                            <div class="mb-2">
                                <label for="required_proof" class="form-label">
                                    Required Proof <small class="text-danger">* Required</small>
                                </label>
                                <textarea class="form-control" name="required_proof" id="required_proof" rows="4" placeholder="Please enter the required proof." required>{{ old('required_proof', $postTask->required_proof) }}</textarea>
                                <div class="invalid-feedback">Please enter the required proof.</div>
                            </div>
                            <div class="mb-2">
                                <label for="additional_note" class="form-label">
                                    Additional Note <small class="text-danger">* Required </small>
                                </label>
                                <textarea class="form-control" name="additional_note" id="additional_note" rows="4" placeholder="Please enter additional notes." required>{{ old('additional_note', $postTask->additional_note) }}</textarea>
                                <small class="text-info">* Please provide your task related information here for verification purposes. This is only admin and you can see it.</small>
                                <div class="invalid-feedback">Please enter additional notes.</div>
                            </div>
                            <div class="mb-2">
                                <label for="thumbnail" class="form-label">
                                    Thumbnail (Optional)
                                </label>
                                <input type="file" class="form-control" name="thumbnail" id="thumbnail" accept=".jpg, .jpeg, .png">
                                <div id="thumbnailError" class="text-danger"></div>
                                <small class="text-info"> * Image format should be jpg, jpeg, png. * Image size should be less than 2MB.</small>
                                @if ($postTask->thumbnail)
                                <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $postTask->thumbnail }}" alt="" id="thumbnailPreview" class="img-fluid mt-2 d-block" style="display:none;" alt="Thumbnail">
                                @endif
                            </div>
                        </section>

                        <!-- Charge & Setting Section -->
                        <h2>Charge & Setting</h2>
                        <section>
                            <div class="row">
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="work_needed" class="form-label">
                                        Work needed <small class="text-danger">* Required </small>
                                    </label>
                                    <input type="number" class="form-control" name="work_needed" min="1" id="work_needed" value="{{ old('work_needed', $postTask->work_needed) }}" required>
                                    <div class="invalid-feedback">Please enter how many workers are required.</div>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="earnings_from_work" class="form-label">
                                        Earnings from work <small class="text-danger">* Required </small>
                                    </label>
                                    <input type="number" class="form-control" name="earnings_from_work" id="earnings_from_work" value="{{ old('earnings_from_work', $postTask->earnings_from_work) }}" required>
                                    <small class="text-info">* Each earnings from work should be within the min charge <strong id="min_task_charge">0</strong> and max charge <strong id="max_task_charge">0</strong>.</small>
                                    <div class="invalid-feedback">Please enter the charges for each worker within the allowed range.</div>
                                    <span id="min_task_charge"></span>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="extra_screenshots" class="form-label">
                                        Extra screenshots <small class="text-danger">* Required </small>
                                    </label>
                                    <input type="number" class="form-control" name="extra_screenshots" min="0" id="extra_screenshots" value="{{ old('extra_screenshots', $postTask->extra_screenshots) }}" required>
                                    <small class="text-info">* Additional screenshot charge is {{ get_default_settings('task_posting_additional_screenshot_charge') }} {{ get_site_settings('site_currency_symbol') }}. Note: You get 1 screenshot for free.</small>
                                    <div class="invalid-feedback">Please enter how many additional screenshots are required</div>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="boosted_time" class="form-label">
                                        Boosted time <small class="text-danger">* Required </small>
                                    </label>
                                    <select class="form-select" name="boosted_time" id="boosted_time" required>
                                        <option value="0" {{ old('boosted_time', $postTask->boosted_time) == 0 ? 'selected' : '' }}>No Boost</option>
                                        <option value="15" {{ old('boosted_time', $postTask->boosted_time) == 15 ? 'selected' : '' }}>15 Minutes</option>
                                        <option value="30" {{ old('boosted_time', $postTask->boosted_time) == 30 ? 'selected' : '' }}>30 Minutes</option>
                                        <option value="45" {{ old('boosted_time', $postTask->boosted_time) == 45 ? 'selected' : '' }}>45 Minutes</option>
                                        <option value="60" {{ old('boosted_time', $postTask->boosted_time) == 60 ? 'selected' : '' }}>1 Hour</option>
                                        <option value="120" {{ old('boosted_time', $postTask->boosted_time) == 120 ? 'selected' : '' }}>2 Hours</option>
                                        <option value="180" {{ old('boosted_time', $postTask->boosted_time) == 180 ? 'selected' : '' }}>3 Hours</option>
                                        <option value="240" {{ old('boosted_time', $postTask->boosted_time) == 240 ? 'selected' : '' }}>4 Hours</option>
                                        <option value="300" {{ old('boosted_time', $postTask->boosted_time) == 300 ? 'selected' : '' }}>5 Hours</option>
                                        <option value="360" {{ old('boosted_time', $postTask->boosted_time) == 360 ? 'selected' : '' }}>6 Hours</option>
                                    </select>
                                    <small class="text-info">* Every 15 minutes boost Charges {{ get_default_settings('task_posting_boosted_time_charge') }} {{ get_site_settings('site_currency_symbol') }}. When the task is boosted, it will be shown at the top of the task list.</small>
                                    <div class="invalid-feedback">Please enter the boosted time.</div>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="running_day" class="form-label">
                                        Running day <small class="text-danger">* Required </small>
                                    </label>
                                    <select class="form-select" name="running_day" id="running_day" required>
                                        <option value="3" {{ old('running_day', $postTask->running_day) == 3 ? 'selected' : '' }}>3 Days</option>
                                        <option value="4" {{ old('running_day', $postTask->running_day) == 4 ? 'selected' : '' }}>4 Days</option>
                                        <option value="5" {{ old('running_day', $postTask->running_day) == 5 ? 'selected' : '' }}>5 Days</option>
                                        <option value="6" {{ old('running_day', $postTask->running_day) == 6 ? 'selected' : '' }}>6 Days</option>
                                        <option value="7" {{ old('running_day', $postTask->running_day) == 7 ? 'selected' : '' }}>1 Week</option>
                                    </select>
                                    <small class="text-info">* When running day is over the task will be closed automatically.</small>
                                    <div class="invalid-feedback">Please enter the running day.</div>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="task_charge" class="form-label">Task charge</label>
                                    <input type="number" class="form-control" id="task_charge" readonly>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="site_charge" class="form-label">Site charge <strong class="text-info">( {{ get_default_settings('task_posting_charge_percentage') }} % )</strong></label>
                                    <input type="number" class="form-control" id="site_charge" readonly>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="total_task_charge" class="form-label">Total task charge</label>
                                    <input type="number" class="form-control" id="total_task_charge" readonly>
                                    <small class="text-info">* Total task charge must be {{ get_default_settings('task_posting_min_budget') }} {{ get_site_settings('site_currency_symbol') }}.</small>
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
        // Initialize the wizard
        $('#wizard').steps({
            headerTag: 'h2',
            bodyTag: 'section',
            transitionEffect: 'slideLeft',
            autoFocus: true,
            labels: {
                finish: "Edit"
            },
            onStepChanging: function(event, currentIndex, newIndex) {
                if (newIndex < currentIndex) return true;

                var form = $('#taskForm');
                var isValid = true;

                if (currentIndex === 1) {
                    var categorySelected = $('input[name="category_id"]:checked').val();
                    if (!categorySelected) {
                        $('#category-options').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#category-options').removeClass('is-invalid');
                    }

                    var subCategorySelected = $('input[name="sub_category_id"]:checked').val();
                    if (categorySelected && !subCategorySelected) {
                        $('#sub-category-options').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#sub-category-options').removeClass('is-invalid');
                    }

                    var childCategoryData = $('#child-category-options').html();
                    var childCategorySelected = $('input[name="child_category_id"]:checked').val();
                    if (subCategorySelected && !childCategorySelected && childCategoryData) {
                        $('#child-category-options').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#child-category-options').removeClass('is-invalid');
                    }

                    return isValid;
                }

                if (currentIndex === 2) {
                    var thumbnail = $('#thumbnail').val();
                    if (thumbnail) {
                        var ext = thumbnail.split('.').pop().toLowerCase();
                        if ($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
                            $('#thumbnailError').text('Please upload a valid image file. Only jpg, jpeg, png files are allowed.');
                            isValid = false;
                        } else if ($('#thumbnail')[0].files[0].size > 2097152) {
                            $('#thumbnailError').text('Please upload an image file less than 2MB.');
                            isValid = false;
                        } else {
                            $('#thumbnailError').text('');
                        }
                    }
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
                var form = $('#taskForm');
                var isValid = true;

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
                var task_posting_min_budget = {{ get_default_settings('task_posting_min_budget') }};
                var user_deposit_balance = {{ Auth::user()->deposit_balance }};
                var total_task_charge = parseFloat($('#total_task_charge').val());

                if(total_task_charge < task_posting_min_budget) {
                    toastr.warning('Your total task Charge must be ' + task_posting_min_budget + ' {{ get_site_settings('site_currency_symbol') }}.');
                    isValid = false;
                }else if (total_task_charge > user_deposit_balance) {
                    toastr.warning('Your balance is not enough to post a task. Please deposit now to post a task.');
                    isValid = false;
                }else{
                    $('#taskForm').submit();
                }
            }
        });

        // thumbnail preview
        $('#thumbnail').change(function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#thumbnailPreview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Load Sub Categories on category change
        function loadSubCategories(category_id) {
            $.ajax({
                url: "{{ route('post_task.get.sub.category') }}",
                type: 'GET',
                data: { category_id: category_id },
                success: function(response) {
                    if (response.sub_categories && response.sub_categories.length > 0) {
                        var options = '';
                        $.each(response.sub_categories, function(index, sub_category) {
                            var isChecked = ({{ $postTask->sub_category_id }} === sub_category.id) ? 'checked' : '';

                            options += '<div class="form-check form-check-inline">';
                            options += '<input type="radio" class="form-check-input" name="sub_category_id" id="sub_category_' + sub_category.id + '" value="' + sub_category.id + '" ' + isChecked + '>';
                            options += '<label class="form-check-label" for="sub_category_' + sub_category.id + '">' + '<span class="badge bg-primary">' + sub_category.name + '</span>' + '</label>';
                            options += '</div>';
                        });
                        $('#sub-category-section').show();
                        $('#sub-category-options').html(options);
                    } else {
                        $('#child-category-section').hide();
                        $('#child-category-options').html('');
                    }

                    var sub_category_id = $('input[name="sub_category_id"]:checked').val();
                    loadChildCategories(category_id, sub_category_id);
                }
            });
        }

        // Load Child Categories on sub category change
        function loadChildCategories(category_id, sub_category_id) {
            $.ajax({
                url: "{{ route('post_task.get.child.category') }}",
                type: 'GET',
                data: { category_id: category_id, sub_category_id: sub_category_id },
                success: function(response) {
                    if (response && response.child_categories && response.child_categories.length > 0) {
                        let options = '';
                        response.child_categories.forEach(child_category => {
                            const isChecked = ({{ $postTask->child_category_id ?? 'null' }} === child_category.id) ? 'checked' : '';

                            options += `<div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="child_category_id" id="child_category_${child_category.id}" value="${child_category.id}" ${isChecked}>
                                            <label class="form-check-label" for="child_category_${child_category.id}">
                                                <span class="badge bg-primary">${child_category.name}</span>
                                            </label>
                                        </div>`;
                        });
                        $('#child-category-options').html(options);
                        $('#child-category-section').show();
                    } else {
                        $('#child-category-section').hide();
                        $('#child-category-options').html('');
                    }

                    const child_category_id = $('input[name="child_category_id"]:checked').val();
                    loadTaskPostCharge(category_id, sub_category_id, child_category_id);
                }
            });
        }

        // Load Task Post Charge on child category change
        function loadTaskPostCharge(category_id, sub_category_id, child_category_id) {
            $.ajax({
                url: "{{ route('post_task.get.task.post.charge') }}",
                type: 'GET',
                data: { category_id: category_id, sub_category_id: sub_category_id, child_category_id: child_category_id },
                success: function(response) {
                    if (response.min_charge && response.max_charge) {
                        $('#earnings_from_work').val(response.min_charge);
                        $('#earnings_from_work').attr('min', response.min_charge);
                        $('#earnings_from_work').attr('max', response.max_charge);
                        $('#min_task_charge').text('{{ get_site_settings('site_currency_symbol') }}' + response.min_charge);
                        $('#max_task_charge').text('{{ get_site_settings('site_currency_symbol') }}' + response.max_charge);
                    }

                    calculateTotalTaskCharge();
                }
            });
        }

        // Get Sub Categories on category change
        $('input[name="category_id"]').change(function() {
            $('#sub-category-section').hide();
            $('#sub-category-options').html('');
            $('#child-category-section').hide();
            $('#child-category-options').html('');
            var category_id = $(this).val();
            loadSubCategories(category_id);
        });

        // Get Child Categories on sub category change
        $(document).on('change', 'input[name="sub_category_id"]', function() {
            $('#child-category-section').hide();
            $('#child-category-options').html('');
            var sub_category_id = $(this).val();
            var category_id = $('input[name="category_id"]:checked').val();
            loadChildCategories(category_id, sub_category_id);
        });

        // Get Task Post Charge on child category change
        $(document).on('change', 'input[name="child_category_id"]', function() {
            var category_id = $('input[name="category_id"]:checked').val();
            var sub_category_id = $('input[name="sub_category_id"]:checked').val();
            var child_category_id = $(this).val();
            loadTaskPostCharge(category_id, sub_category_id, child_category_id);
        });

        // Add change event for all input and select fields in the form
        $('#taskForm').on('change', 'input, select', function() {
            calculateTotalTaskCharge();
        });

        // Add keyup event for work_needed, earnings_from_work, and extra_screenshots fields
        $('#work_needed, #earnings_from_work, #extra_screenshots').on('keyup', function() {
            calculateTotalTaskCharge();
        });

        // Calculate the total task charge
        function calculateTotalTaskCharge() {
            var work_needed = parseInt($('#work_needed').val());
            var earnings_from_work = parseFloat($('#earnings_from_work').val());
            var extra_screenshots = parseInt($('#extra_screenshots').val());
            var boosted_time = parseInt($('#boosted_time').val());
            var screenshot_charge = {{ get_default_settings('task_posting_additional_screenshot_charge') }};
            var boosted_time_charge = {{ get_default_settings('task_posting_boosted_time_charge') }};
            var task_posting_charge_percentage = {{ get_default_settings('task_posting_charge_percentage') }};

            var task_charge = (work_needed * earnings_from_work) +
                (extra_screenshots * screenshot_charge) +
                ((boosted_time / 15) * boosted_time_charge);

            $('#task_charge').val(task_charge.toFixed(2));
            var site_charge = (task_charge * task_posting_charge_percentage / 100);
            $('#site_charge').val(site_charge.toFixed(2));
            $('#total_task_charge').val((task_charge + site_charge).toFixed(2));
        }

        // Validate the input fields
        function validateInputFields() {
            let isValid = true;

            // Validate work_needed
            let work_needed = parseInt($('#work_needed').val());
            if (isNaN(work_needed) || work_needed < 1) {
                $('#work_needed').addClass('is-invalid');
                isValid = false;
            } else {
                $('#work_needed').removeClass('is-invalid');
            }

            // Validate earnings_from_work
            let earnings_from_work = parseFloat($('#earnings_from_work').val());
            let minCharge = parseFloat($('#earnings_from_work').attr('min'));
            if (isNaN(earnings_from_work) || earnings_from_work < minCharge) {
                $('#earnings_from_work').addClass('is-invalid');
                isValid = false;
            } else {
                $('#earnings_from_work').removeClass('is-invalid');
            }

            // Validate extra_screenshots
            let extraScreenshots = parseInt($('#extra_screenshots').val());
            if (isNaN(extraScreenshots) || extraScreenshots < 0) {
                $('#extra_screenshots').addClass('is-invalid');
                isValid = false;
            } else {
                $('#extra_screenshots').removeClass('is-invalid');
            }

            return isValid;
        }

        // Initialize the total task Charge on page load
        calculateTotalTaskCharge();

        // Load the sub categories on page load
        var category_id = $('input[name="category_id"]:checked').val();
        loadSubCategories(category_id);
    });
</script>
@endsection

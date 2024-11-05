@extends('layouts.template_master')

@section('title', 'Post Task')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Post Task</h4>
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
                <form id="taskForm" action="{{ route('post_task.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div id="wizard">
                        <!-- Notice Section -->
                        <h2>Notice</h2>
                        <section>
                            @if (Auth::user()->deposit_balance < get_default_settings('task_posting_min_budget'))
                            <div class="alert alert-warning mb-3">
                                Your current balance is {{ get_site_settings('site_currency_symbol') }} {{ Auth::user()->deposit_balance }}. You need to pay {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_min_budget') }} to post a task. Your balance is not enough to post a task. Please deposit now to post a task.
                                <a href="{{ route('deposit') }}" class="text-primary">Deposit Now</a>
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
                                        <input type="radio" class="form-check-input" name="category_id" id="category_{{ $category->id }}" value="{{ $category->id }}" required>
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
                                <textarea class="form-control" name="title" id="title" rows="1" placeholder="Please enter a title." required></textarea>
                                <div class="invalid-feedback">Please enter a title.</div>
                                <small class="text-danger" id="title_error"></small>
                            </div>
                            <div class="mb-2">
                                <label for="description" class="form-label">
                                    Description <small class="text-danger">* Required</small>
                                </label>
                                <textarea class="form-control" name="description" id="description" rows="4" placeholder="Please enter a description." required></textarea>
                                <div class="invalid-feedback">Please enter a description.</div>
                            </div>
                            <div class="mb-2">
                                <label for="required_proof" class="form-label">
                                    Required Proof <small class="text-danger">* Required</small>
                                </label>
                                <textarea class="form-control" name="required_proof" id="required_proof" rows="4" placeholder="Please enter the required proof." required></textarea>
                                <div class="invalid-feedback">Please enter the required proof.</div>
                            </div>
                            <div class="mb-2">
                                <label for="additional_note" class="form-label">
                                    Additional Note <small class="text-danger">* Required </small>
                                </label>
                                <textarea class="form-control" name="additional_note" id="additional_note" rows="4" placeholder="Please enter additional notes." required></textarea>
                                <div class="invalid-feedback">Please enter additional notes.</div>
                                <small class="text-info">* Please provide your task related information here for verification purposes. This is only admin and you can see it.</small>
                            </div>
                            <div class="mb-2">
                                <label for="thumbnail" class="form-label">
                                    Thumbnail (Optional)
                                </label>
                                <input type="file" class="form-control" name="thumbnail" id="thumbnail" accept=".jpg, .jpeg, .png">
                                <div id="thumbnailError" class="text-danger"></div>
                                <small class="text-info d-block"> * Image format should be jpg, jpeg, png. * Image size should be less than 2MB.</small>
                                <img src="" alt="Thumbnail" id="thumbnailPreview" class="mt-2" style="height: 320px; display: none;" alt="Thumbnail">
                            </div>
                        </section>

                        <!-- Charge & Setting Section -->
                        <h2>Charge & Setting</h2>
                        <section>
                            <div class="row">
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="work_needed" class="form-label">
                                        Work Needed <small class="text-danger">* Required </small>
                                    </label>
                                    <input type="number" class="form-control" name="work_needed" id="work_needed" min="1" value="1" placeholder="Please enter how many workers are required." required>
                                    <div class="invalid-feedback">Please enter how many workers are required.</div>
                                    <small class="text-danger" id="work_needed_error"></small>
                                    <small class="text-info d-block">* Minimum work needed is 1.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="earnings_from_work" class="form-label">
                                        Earnings From Work <small class="text-danger">* Required </small>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="earnings_from_work" id="earnings_from_work" placeholder="Please enter the charges for each worker." required>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                        <div class="invalid-feedback">Please enter charges for each worker are required</div>
                                    </div>
                                    <small class="text-danger" id="earnings_from_work_error"></small>
                                    <small class="text-info d-block">* Each earnings from work should be within the min charge <strong id="min_charge">0</strong> and max charge <strong id="max_charge">0</strong>.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="extra_screenshots" class="form-label">
                                        Extra Screenshots <small class="text-danger">* Required </small>
                                    </label>
                                    <input type="number" class="form-control" name="extra_screenshots" id="extra_screenshots" min="0" value="0" placeholder="Please enter how many additional screenshots are required." required>
                                    <div class="invalid-feedback">Please enter how many additional screenshots are required</div>
                                    <small class="text-danger" id="extra_screenshots_error"></small>
                                    <small class="text-info d-block">* Additional screenshot charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_screenshot_charge') }} per screenshot. Note: You get 1 screenshot for free.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="boosted_time" class="form-label">
                                        Boosted Time <small class="text-danger">* Required </small>
                                    </label>
                                    <select class="form-select" name="boosted_time" id="boosted_time" required>
                                        <option value="0" selected>No Boost</option>
                                        <option value="15">15 Minutes</option>
                                        <option value="30">30 Minutes</option>
                                        <option value="45">45 Minutes</option>
                                        <option value="60">1 Hour</option>
                                        <option value="120">2 Hours</option>
                                        <option value="180">3 Hours</option>
                                        <option value="240">4 Hours</option>
                                        <option value="300">5 Hours</option>
                                        <option value="360">6 Hours</option>
                                    </select>
                                    <div class="invalid-feedback">Please enter the boosted time.</div>
                                    <small class="text-info">* Every 15 minutes boost charges {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_boosted_time_charge') }}.</small>
                                    <br>
                                    <small class="text-info">* When the task is boosted, it will be shown at the top of the task list.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="work_duration" class="form-label">
                                        Work Duration <small class="text-danger">* Required </small>
                                    </label>
                                    <select class="form-select" name="work_duration" id="work_duration" required>
                                        <option value="3" selected>3 Days</option>
                                        <option value="4">4 Days</option>
                                        <option value="5">5 Days</option>
                                        <option value="6">6 Days</option>
                                        <option value="7">1 Week</option>
                                    </select>
                                    <div class="invalid-feedback">Please enter the work duration.</div>
                                    <small class="text-info">* Additional work duration charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_work_duration_charge') }} per day. Note: You get 3 days for free.</small>
                                    <br>
                                    <small class="text-info">* When work duration is over the task will be canceled automatically.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="task_charge" class="form-label">Task Charge</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="task_charge" readonly>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="site_charge" class="form-label">Site Charge</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="site_charge" readonly>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                    </div>
                                    <small class="text-info">* Site charge is {{ get_default_settings('task_posting_charge_percentage') }} % of the task charge.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="total_task_charge" class="form-label">Total Task Charge</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="total_task_charge" readonly>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                    </div>
                                    <small class="text-info">* Total task charge must be {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_min_budget') }}.</small>
                                </div>
                            </div>

                            <div class="alert alert-danger text-center" role="alert" id="task_create_message">
                            </div>

                            @if (Auth::user()->deposit_balance < get_default_settings('task_posting_min_budget'))
                            <div class="alert alert-warning">
                                Your current balance is {{ get_site_settings('site_currency_symbol') }} {{ Auth::user()->deposit_balance }}. You need to pay {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_min_budget') }} to post a task. Your balance is not enough to post a task. Please deposit now to post a task.
                                <a href="{{ route('deposit') }}" class="text-primary">Deposit Now</a>
                            </div>
                            @endif
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
        // Real-time validation for radio buttons
        $('#taskForm').on('change', 'input[name="category_id"]', function() {
            var categorySelected = $('input[name="category_id"]:checked').val();
            if (!categorySelected) {
                $('#category-options').addClass('is-invalid');
            } else {
                $('#category-options').removeClass('is-invalid');
                $('input[name="sub_category_id"]').prop('checked', false);
                $('#sub-category-options').addClass('is-invalid');
            }
        });
        $('#taskForm').on('change', 'input[name="sub_category_id"]', function() {
            var subCategorySelected = $('input[name="sub_category_id"]:checked').val();
            if (!subCategorySelected) {
                $('#sub-category-options').addClass('is-invalid');
            } else {
                $('#sub-category-options').removeClass('is-invalid');
                $('input[name="child_category_id"]').prop('checked', false);
                $('#child-category-options').addClass('is-invalid');
            }
        });
        $('#taskForm').on('change', 'input[name="child_category_id"]', function() {
            var childCategorySelected = $('input[name="child_category_id"]:checked').val();
            if (!childCategorySelected) {
                $('#child-category-options').addClass('is-invalid');
            } else {
                $('#child-category-options').removeClass('is-invalid');
            }
        });

        // Real-time validation for input fields
        $('#taskForm').on('input', 'input, textarea', function() {
            // Add is-invalid class if the field is invalid
            if ($(this).is(':invalid')) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }

            // Validate the title field
            var title = $('#title').val();
            if (title.length > 255) {
                $(this).removeClass('is-invalid');
                $('#title_error').text('Title length should be less than 255 characters. You have entered ' + title.length + ' characters.');
            } else {
                $('#title_error').text('');
            }

            // Validate the work needed field
            var work_needed = parseInt($('#work_needed').val());
            if (work_needed < 1) {
                $('#work_needed').removeClass('is-invalid');
                $('#work_needed_error').text('Work needed should be greater than or equal to 1.');
            } else {
                $('#work_needed_error').text('');
            }

            // Validate the earnings from work field
            var earnings_from_work = parseFloat($('#earnings_from_work').val());
            var minCharge = parseFloat($('#earnings_from_work').attr('min'));
            var maxCharge = parseFloat($('#earnings_from_work').attr('max'));
            if (earnings_from_work < minCharge) {
                $('#earnings_from_work').removeClass('is-invalid');
                $('#earnings_from_work_error').text('Earnings from work should be greater than or equal to ' + ' {{ get_site_settings('site_currency_symbol') }} ' + minCharge + '.');
            } else if (earnings_from_work > maxCharge) {
                $('#earnings_from_work').removeClass('is-invalid');
                $('#earnings_from_work_error').text('Earnings from work should be less than or equal to ' + ' {{ get_site_settings('site_currency_symbol') }} ' + maxCharge + '.');
            } else {
                $('#earnings_from_work_error').text('');
            }

            // Validate the extra screenshots field
            var extra_screenshots = parseInt($('#extra_screenshots').val());
            if (extra_screenshots < 0) {
                $('#extra_screenshots').removeClass('is-invalid');
                $('#extra_screenshots_error').text('Extra screenshots should be greater than or equal to 0.');
            } else {
                $('#extra_screenshots_error').text('');
            }
        });

        // Initialize the wizard
        $('#wizard').steps({
            headerTag: 'h2',
            bodyTag: 'section',
            transitionEffect: 'slideLeft',
            autoFocus: true,
            labels: {
                finish: "Create"
            },
            onStepChanging: function(event, currentIndex, newIndex) {
                if (newIndex < currentIndex) return true;

                var form = $('#taskForm');
                var isValid = true;

                // Validate the input fields
                form.find('section').eq(currentIndex).find(':input[required]').each(function() {
                    if (!this.checkValidity()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (currentIndex === 1) {
                    // Validate the category fields
                    var categorySelected = $('input[name="category_id"]:checked').val();
                    if (!categorySelected) {
                        $('#category-options').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#category-options').removeClass('is-invalid');
                    }
                    // Validate the sub category fields
                    var subCategorySelected = $('input[name="sub_category_id"]:checked').val();
                    if (categorySelected && !subCategorySelected) {
                        $('#sub-category-options').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#sub-category-options').removeClass('is-invalid');
                    }
                    // Validate the child category fields
                    var childCategoryData = $('#child-category-options').html();
                    var childCategorySelected = $('input[name="child_category_id"]:checked').val();
                    if (subCategorySelected && !childCategorySelected && childCategoryData) {
                        $('#child-category-options').addClass('is-invalid');
                        isValid = false;
                    } else {
                        $('#child-category-options').removeClass('is-invalid');
                    }
                }

                if (currentIndex === 2) {
                    // Validate the title field
                    var title = $('#title').val();
                    if (title.length > 255) {
                        $('#title_error').text('Title length should be less than 255 characters. You have entered ' + title.length + ' characters.');
                        isValid = false;
                    } else {
                        $('#title_error').text('');
                    }
                    // Validate the thumbnail file
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

                return isValid;
            },
            onFinishing: function(event, currentIndex) {
                var form = $('#taskForm');
                var isValid = true;

                // Validate the input fields
                form.find(':input[required]').each(function() {
                    if (!this.checkValidity()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                // Validate the work needed field
                var work_needed = parseInt($('#work_needed').val());
                if (work_needed < 1) {
                    $('#work_needed').removeClass('is-invalid');
                    $('#work_needed_error').text('Work needed should be greater than or equal to 1.');
                    isValid = false;
                } else {
                    $('#work_needed_error').text('');
                }
                // Validate the earnings from work field
                var earnings_from_work = parseFloat($('#earnings_from_work').val());
                var minCharge = parseFloat($('#earnings_from_work').attr('min'));
                var maxCharge = parseFloat($('#earnings_from_work').attr('max'));
                if (earnings_from_work < minCharge) {
                    $('#earnings_from_work').removeClass('is-invalid');
                    $('#earnings_from_work_error').text('Earnings from work should be greater than or equal to ' + ' {{ get_site_settings('site_currency_symbol') }} ' + minCharge + '.');
                    isValid = false;
                } else if (earnings_from_work > maxCharge) {
                    $('#earnings_from_work').removeClass('is-invalid');
                    $('#earnings_from_work_error').text('Earnings from work should be less than or equal to ' + ' {{ get_site_settings('site_currency_symbol') }} ' + maxCharge + '.');
                    isValid = false;
                } else {
                    $('#earnings_from_work_error').text('');
                }
                // Validate the extra screenshots field
                var extra_screenshots = parseInt($('#extra_screenshots').val());
                if (extra_screenshots < 0) {
                    $('#extra_screenshots').removeClass('is-invalid');
                    $('#extra_screenshots_error').text('Extra screenshots should be greater than or equal to 0.');
                    isValid = false;
                } else {
                    $('#extra_screenshots_error').text('');
                }

                return isValid;
            },
            onFinished: function(event, currentIndex) {
                var task_posting_min_budget = {{ get_default_settings('task_posting_min_budget') }};
                var user_deposit_balance = {{ Auth::user()->deposit_balance }};
                var total_task_charge = parseFloat($('#total_task_charge').val());

                if(total_task_charge < task_posting_min_budget) {
                    toastr.warning('Your total task charge must be ' + ' {{ get_site_settings('site_currency_symbol') }} ' + task_posting_min_budget + '. Please increase the task charge to post a task.');
                    isValid = false;
                }else if (total_task_charge > user_deposit_balance) {
                    toastr.warning('Your balance is not enough to post a task. Please deposit now to post a task.');
                    isValid = false;
                }else{
                    $('#taskForm').submit();

                    // Disable the submit button to prevent multiple submissions
                    $('#wizard').find('.actions a[href="#finish"]').addClass('disabled');

                    // Hide the finish button
                    $('#wizard').find('.actions a[href="#finish"]').hide();

                    // Show the loading spinner
                    $('#wizard').find('.actions ul').append('<strong class="btn btn-primary"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...</strong>');
                }
            }
        });

        // thumbnail preview and validation
        document.getElementById('thumbnail').addEventListener('change', function() {
            const file = this.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file && allowedTypes.includes(file.type)) {
                if (file.size > maxSize) {
                    $('#thumbnailError').text('File size is too large. Max size is 2MB.');
                    this.value = ''; // Clear file input
                    // Hide preview image
                    $('#thumbnailPreview').hide();
                } else {
                    $('#thumbnailError').text('');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#thumbnailPreview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                $('#thumbnailError').text('Please select a valid image file (jpeg, jpg, png).');
                this.value = ''; // Clear file input
                // Hide preview image
                $('#thumbnailPreview').hide();
            }
        });

        // Add change event for category radio buttons
        $('input[name="category_id"]').change(function() {
            var category_id = $(this).val();
            $('#child-category-section').hide();
            $('#child-category-options').html('');
            $.ajax({
                url: "{{ route('post_task.get.sub.category') }}",
                type: 'GET',
                data: { category_id: category_id },
                success: function(response) {
                    if (response.sub_categories && response.sub_categories.length > 0) {
                        var options = '';
                        $.each(response.sub_categories, function(index, sub_category) {
                            options += '<div class="form-check form-check-inline">';
                            options += '<input type="radio" class="form-check-input" name="sub_category_id" id="sub_category_' + sub_category.id + '" value="' + sub_category.id + '">';
                            options += '<label class="form-check-label" for="sub_category_' + sub_category.id + '">' + '<span class="badge bg-primary">' + sub_category.name + '</span>' + '</label>';
                            options += '</div>';
                        });
                        $('#sub-category-section').show();
                        $('#sub-category-options').html(options);
                    } else {
                        $('#child-category-options').html('');
                    }
                }
            });
        });

        // Add change event for sub category radio buttons
        $(document).on('change', 'input[name="sub_category_id"]', function() {
            var sub_category_id = $(this).val();
            var category_id = $('input[name="category_id"]:checked').val();
            $.ajax({
                url: "{{ route('post_task.get.child.category') }}",
                type: 'GET',
                data: { category_id: category_id, sub_category_id: sub_category_id },
                success: function(response) {
                    if (response.child_categories && response.child_categories.length > 0) {
                        var options = '';
                        $.each(response.child_categories, function(index, child_category) {
                            options += '<div class="form-check form-check-inline">';
                            options += '<input type="radio" class="form-check-input" name="child_category_id" id="child_category_' + child_category.id + '" value="' + child_category.id + '">';
                            options += '<label class="form-check-label" for="child_category_' + child_category.id + '">' + '<span class="badge bg-primary">' + child_category.name + '</span>'  + '</label>';
                            options += '</div>';
                        });
                        $('#child-category-options').html(options);
                        $('#child-category-section').show();
                    } else {
                        $('#child-category-section').hide();
                        $('#child-category-options').html('');
                    }

                    loadTaskPostCharge(category_id, sub_category_id, null);
                    calculateTotalTaskCharge();
                }
            });
        });

        // Add change event for child category radio buttons
        $(document).on('change', 'input[name="child_category_id"]', function() {
            var category_id = $('input[name="category_id"]:checked').val();
            var sub_category_id = $('input[name="sub_category_id"]:checked').val();
            var child_category_id = $(this).val();
            if (child_category_id) {
                loadTaskPostCharge(category_id, sub_category_id, child_category_id);
            }
        });

        // Add change event for earnings_from_work input field
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
                        $('#min_charge').text(' {{ get_site_settings('site_currency_symbol') }} ' + response.min_charge);
                        $('#max_charge').text(' {{ get_site_settings('site_currency_symbol') }} ' + response.max_charge);
                    }

                    calculateTotalTaskCharge();
                }
            });
        }

        // Add change event for input and select fields
        $('#taskForm').on('change', 'input, select', function() {
            calculateTotalTaskCharge();
        });

        // Add keyup event for work_needed, earnings_from_work, and extra_screenshots fields
        $('#work_needed, #earnings_from_work, #extra_screenshots').on('keyup', function() {
            calculateTotalTaskCharge();
        });

        // Calculate the total task charge based on the input fields
        function calculateTotalTaskCharge() {
            var work_needed = parseInt($('#work_needed').val()) || 0;
            var earnings_from_work = parseFloat($('#earnings_from_work').val()) || 0;

            var extra_screenshots = parseInt($('#extra_screenshots').val()) || 0;
            var screenshot_charge = {{ get_default_settings('task_posting_additional_screenshot_charge') }};

            var boosted_time = parseInt($('#boosted_time').val()) || 0;
            var boosted_time_charge = {{ get_default_settings('task_posting_boosted_time_charge') }};

            var work_duration = parseInt($('#work_duration').val()) || 0;
            var work_duration_charge = {{ get_default_settings('task_posting_additional_work_duration_charge') }};

            var task_posting_charge_percentage = {{ get_default_settings('task_posting_charge_percentage') }};
            var task_posting_min_budget = {{ get_default_settings('task_posting_min_budget') }};

            var total_screenshot_charge = screenshot_charge * extra_screenshots;
            var total_boosted_time_charge = boosted_time_charge * (boosted_time / 15);
            var total_work_duration_charge = work_duration_charge * (work_duration - 3);

            var task_charge = (work_needed * earnings_from_work) + total_screenshot_charge + total_boosted_time_charge + total_work_duration_charge;
            $('#task_charge').val(task_charge.toFixed(2));

            var site_charge = (task_charge * task_posting_charge_percentage) / 100;
            $('#site_charge').val(site_charge.toFixed(2));

            var total_task_charge = (task_charge + site_charge).toFixed(2);
            $('#total_task_charge').val(total_task_charge);

            // Calculate the increase charge
            var increase_charge = (task_posting_min_budget - total_task_charge).toFixed(2);
            if (increase_charge > 0) {
                $('#task_create_message').html('<strong>Posting this task need more  ' + ' {{ get_site_settings('site_currency_symbol') }} ' + increase_charge + ' total task charge. Then you can submit the task.</strong>');
                $('#task_create_message').addClass('alert-danger');
                $('#task_create_message').removeClass('alert-success');
            }else{
                $('#task_create_message').html('<strong>You can submit the task now by clicking the Create button.</strong>');
                $('#task_create_message').removeClass('alert-danger');
                $('#task_create_message').addClass('alert-success');
            }

            // Validate the total task charge
            if (total_task_charge < task_posting_min_budget) {
                // desible the submit button
                $('#wizard').find('.actions a[href="#finish"]').addClass('disabled');
            } else {
                // enable the submit button
                $('#wizard').find('.actions a[href="#finish"]').removeClass('disabled');
            }
        }

        // Initialize the total task Charge on page load
        calculateTotalTaskCharge();
    });
</script>
@endsection

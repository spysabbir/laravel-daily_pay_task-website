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
                                <span>Rejected At: {{ date('d M, Y h:i:s A', strtotime($postTask->rejected_at)) }}</span> <br>
                                <span>Rejection Reason: {{ $postTask->rejection_reason }}</span> <br>
                            </div>
                            @endif
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
                                        <input type="radio" class="form-check-input" name="category_id" id="category_{{ $category->id }}" value="{{ $category->id }}" {{ $postTask->category_id == $category->id ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            <span class="badge">{{ $category->name }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <small class="text-danger" id="category_error"></small>
                            </div>
                            <div class="mb-3 border p-2" id="sub-category-section" style="display:none;">
                                <h5 class="bg-dark text-center py-1 mb-3 rounded">
                                    Select Sub Category <small class="text-danger">* Required</small>
                                </h5>
                                <div id="sub-category-options">
                                    <!-- Sub-category radio buttons will be loaded here -->
                                </div>
                                <small class="text-danger" id="sub_category_error"></small>
                            </div>
                            <div class="mb-3 border p-2" id="child-category-section" style="display:none;">
                                <h5 class="bg-dark text-center py-1 mb-3 rounded">
                                    Select Child Category <small class="text-danger">* Required</small>
                                </h5>
                                <div id="child-category-options">
                                    <!-- Child-category radio buttons will be loaded here -->
                                </div>
                                <small class="text-danger" id="child_category_error"></small>
                            </div>
                        </section>

                        <!-- Task Information Section -->
                        <h2>Task Information</h2>
                        <section>
                            <div class="mb-2">
                                <label for="title" class="form-label">
                                    Title <small class="text-danger">* Required</small>
                                </label>
                                <textarea class="form-control" name="title" id="title" rows="1" placeholder="Please enter a title." required>{{ old('title', $postTask->title) }}</textarea>
                                <small class="text-danger" id="title_error"></small>
                                <small class="text-info d-block">*Note: Only 255 characters are allowed.</small>
                            </div>
                            <div class="mb-2">
                                <label for="description" class="form-label">
                                    Description <small class="text-danger">* Required</small>
                                </label>
                                <textarea class="form-control" name="description" id="description" rows="4" placeholder="Please enter a description." required>{{ old('description', $postTask->description) }}</textarea>
                                <small class="text-danger" id="description_error"></small>
                            </div>
                            <div class="mb-2">
                                <label for="required_proof_answer" class="form-label">
                                    Required Proof Answer <small class="text-danger">* Required</small>
                                </label>
                                <textarea class="form-control" name="required_proof_answer" id="required_proof_answer" rows="4" placeholder="Please enter the required proof answer." required>{{ old('required_proof_answer', $postTask->required_proof_answer) }}</textarea>
                                <small class="text-danger" id="description_error"></small>
                            </div>
                            <div class="mb-2">
                                <label for="additional_note" class="form-label">
                                    Additional Note <small class="text-danger">* Required </small>
                                </label>
                                <textarea class="form-control" name="additional_note" id="additional_note" rows="4" placeholder="Please enter additional notes." required>{{ old('additional_note', $postTask->additional_note) }}</textarea>
                                <small class="text-danger" id="additional_note_error"></small>
                                <small class="text-info d-block">*Note: Please provide additional note information about your task here for verification purposes and if you need any question answered from worker please enter the answer here, Only admin and you can see.</small>
                            </div>
                            <div class="mb-2">
                                <label for="thumbnailEdit" class="form-label">
                                    Thumbnail (Optional)
                                </label>
                                <input type="file" class="form-control" name="thumbnail" id="thumbnailEdit" accept=".jpg, .jpeg, .png">
                                <small id="thumbnailError" class="text-danger"></small>
                                <small class="text-info"> * Image format should be jpg, jpeg, png. * Image size should be less than 2MB.</small>
                                @if ($postTask->thumbnail)
                                <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $postTask->thumbnail }}" alt="Thumbnail" class="img-fluid mt-2 d-block" style="display:none;" alt="Thumbnail">
                                @else
                                <img src="" alt="" id="thumbnailPreviewEdit" class="img-fluid mt-2 d-block" style="display:none;" alt="Thumbnail">
                                @endif
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
                                    <input type="number" class="form-control" name="work_needed" min="1" id="work_needed" value="{{ old('work_needed', $postTask->work_needed) }}" placeholder="Please enter how many workers are required." required>
                                    <small class="text-danger" id="work_needed_error"></small>
                                    <small class="text-info d-block">* Minimum work needed is 1.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="earnings_from_work" class="form-label">
                                        Earnings From Work <small class="text-danger">* Required </small>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="earnings_from_work" id="earnings_from_work" value="{{ old('earnings_from_work', $postTask->earnings_from_work) }}" placeholder="Please enter the charges for each worker." required>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                    </div>
                                    <small class="text-danger" id="earnings_from_work_error"></small>
                                    <small class="text-info d-block">* Each earnings from work should be within the min charge <strong id="min_task_charge">0</strong> and max charge <strong id="max_task_charge">0</strong>.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="required_proof_photo" class="form-label">
                                        Required Proof Photo <small class="text-danger">* Required </small>
                                    </label>
                                    <input type="number" class="form-control" name="required_proof_photo" min="0" id="required_proof_photo" value="{{ old('required_proof_photo', $postTask->required_proof_photo) }}" placeholder="Please enter how many additional required proof photo are required." required>
                                    <small class="text-danger" id="required_proof_photo_error"></small>
                                    <small class="text-info d-block">* Additional required proof photo charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_required_proof_photo_charge') }} per required proof photo.</small>
                                    <small class="text-info d-block">* Note: You get 1 required proof photo for free.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="boosted_time" class="form-label">
                                        Boosted Time <small class="text-danger">* Required </small>
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
                                    <small class="text-danger" id="work_duration_error"></small>
                                    <small class="text-info">* Every 15 minutes boost charges {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_boosted_time_charge') }}.</small>
                                    <br>
                                    <small class="text-info">* When the task is boosted, it will be shown at the top of the task list.</small>
                                </div>
                                <div class="col-lg-6 col-12 mb-3">
                                    <label for="work_duration" class="form-label">
                                        Work Duration <small class="text-danger">* Required </small>
                                    </label>
                                    <select class="form-select" name="work_duration" id="work_duration" required>
                                        <option value="3" {{ old('work_duration', $postTask->work_duration) == 3 ? 'selected' : '' }}>3 Days</option>
                                        <option value="4" {{ old('work_duration', $postTask->work_duration) == 4 ? 'selected' : '' }}>4 Days</option>
                                        <option value="5" {{ old('work_duration', $postTask->work_duration) == 5 ? 'selected' : '' }}>5 Days</option>
                                        <option value="6" {{ old('work_duration', $postTask->work_duration) == 6 ? 'selected' : '' }}>6 Days</option>
                                        <option value="7" {{ old('work_duration', $postTask->work_duration) == 7 ? 'selected' : '' }}>1 Week</option>
                                    </select>
                                    <small class="text-danger" id="work_duration_error"></small>
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

<style>
    .form-check-input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .form-check-label {
        cursor: pointer;
    }
    .form-check-label .badge {
        background-color: #007bff;
    }
    .form-check-input[type="radio"]:checked + .form-check-label .badge {
        background-color: rgb(5, 163, 74);
    }
</style>

@section('script')
<script>
    $(document).ready(function() {
        // Real-time validation 1st step
        $('#taskForm').on('change', 'input[name="category_id"]', function() {
            var categorySelected = $('input[name="category_id"]:checked').val();
            if (!categorySelected) {
                $('#category_error').text('Please select a category.');
            } else {
                $('#category_error').text('');
                $('input[name="sub_category_id"]').prop('checked', false);
                $('#sub_category_error').text('Please select a sub category.');
            }
        });
        $('#taskForm').on('change', 'input[name="sub_category_id"]', function() {
            var subCategorySelected = $('input[name="sub_category_id"]:checked').val();
            if (!subCategorySelected) {
                $('#sub_category_error').text('Please select a sub category.');
            } else {
                $('#sub_category_error').text('');
                $('input[name="child_category_id"]').prop('checked', false);
                $('#child_category_error').text('Please select a child category.');
            }
        });
        $('#taskForm').on('change', 'input[name="child_category_id"]', function() {
            var childCategorySelected = $('input[name="child_category_id"]:checked').val();
            if (!childCategorySelected) {
                $('#child_category_error').text('Please select a child category.');
            } else {
                $('#child_category_error').text('');
            }
        });

        // Real-time validation 2nd step
        $('#taskForm').on('input', '#title, #description, #required_proof_answer, #additional_note', function() {
            // Validate the title field
            var title = $('#title').val();
            if (!title) {
                $('#title_error').text('Title is required.');
            } else if (title.length > 255) {
                $('#title_error').text('Title length should be less than 255 characters. You have entered ' + title.length + ' characters.');
            } else {
                $('#title_error').text('');
            }
            // Validate the description field
            var description = $('#description').val();
            if (!description) {
                $('#description_error').text('Description is required.');
            } else {
                $('#description_error').text('');
            }
            // Validate the required proof answer field
            var required_proof_answer = $('#required_proof_answer').val();
            if (!required_proof_answer) {
                $('#required_proof_answer_error').text('Required proof answer is required.');
            } else {
                $('#required_proof_answer_error').text('');
            }
            // Validate the additional note field
            var additional_note = $('#additional_note').val();
            if (!additional_note) {
                $('#additional_note_error').text('Additional note is required.');
            } else {
                $('#additional_note_error').text('');
            }
        });

        // Real-time validation 3rd step
        $('#taskForm').on('input', '#work_needed, #earnings_from_work, #required_proof_photo', function() {
            // Validate the work needed field
            var work_needed = parseInt($('#work_needed').val());
            if (isNaN(work_needed)) {
                $('#work_needed_error').text('Please enter how many workers are required.');
            } else if (work_needed < 1) {
                $('#work_needed_error').text('Work needed should be greater than or equal to 1.');
            } else {
                $('#work_needed_error').text('');
            }
            // Validate the earnings from work field
            var earnings_from_work = parseFloat($('#earnings_from_work').val());
            var minCharge = parseFloat($('#earnings_from_work').attr('min'));
            var maxCharge = parseFloat($('#earnings_from_work').attr('max'));
            if (isNaN(earnings_from_work)) {
                $('#earnings_from_work_error').text('Please enter charges for each worker are required.');
            } else if (earnings_from_work < minCharge) {
                $('#earnings_from_work_error').text('Earnings from work should be greater than or equal to {{ get_site_settings("site_currency_symbol") }} ' + minCharge + '.');
            } else if (earnings_from_work > maxCharge) {
                $('#earnings_from_work_error').text('Earnings from work should be less than or equal to {{ get_site_settings("site_currency_symbol") }} ' + maxCharge + '.');
            } else {
                $('#earnings_from_work_error').text('');
            }
            // Validate the extra required proof photo field
            var required_proof_photo = parseInt($('#required_proof_photo').val());
            if (isNaN(required_proof_photo)) {
                $('#required_proof_photo_error').text('Please enter how many additional required proof photo are required.');
            } else if (required_proof_photo < 0) {
                $('#required_proof_photo_error').text('Extra required proof photo should be greater than or equal to 0.');
            } else {
                $('#required_proof_photo_error').text('');
            }
        });
        $('#taskForm').on('change', '#boosted_time, #work_duration', function() {
            // Validate the boosted time field
            var boosted_time = parseInt($('#boosted_time').val());
            if (isNaN(boosted_time)) {
                $('#boosted_time_error').text('Please select a boosted time.');
            } else if (boosted_time < 0) {
                $('#boosted_time_error').text('Boosted time should be greater than or equal to 0.');
            } else {
                $('#boosted_time_error').text('');
            }
            // Validate the work duration field
            var work_duration = parseInt($('#work_duration').val());
            if (isNaN(work_duration)) {
                $('#work_duration_error').text('Please select a work duration.');
            } else if (work_duration < 3) {
                $('#work_duration_error').text('Work duration should be greater than or equal to 3.');
            } else {
                $('#work_duration_error').text('');
            }
        });

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
                    // Validate the category fields
                    var categorySelected = $('input[name="category_id"]:checked').val();
                    if (!categorySelected) {
                        $('#category_error').text('Please select a category.');
                        isValid = false;
                    } else {
                        $('#category_error').text('');
                    }
                    // Validate the sub category fields
                    var subCategorySelected = $('input[name="sub_category_id"]:checked').val();
                    if (categorySelected && !subCategorySelected) {
                        $('#sub_category_error').text('Please select a sub category.');
                        isValid = false;
                    } else {
                        $('#sub_category_error').text('');
                    }
                    // Validate the child category fields
                    var childCategoryData = $('#child-category-options').html();
                    var childCategorySelected = $('input[name="child_category_id"]:checked').val();
                    if (subCategorySelected && !childCategorySelected && childCategoryData) {
                        $('#child_category_error').text('Please select a child category.');
                        isValid = false;
                    } else {
                        $('#child_category_error').text('');
                    }
                }

                if (currentIndex === 2) {
                    // Validate the title field
                    var title = $('#title').val();
                    if (!title) {
                        $('#title_error').text('Title is required.');
                        isValid = false;
                    } else if (title.length > 255) {
                        $('#title_error').text('Title length should be less than 255 characters. You have entered ' + title.length + ' characters.');
                        isValid = false;
                    } else {
                        $('#title_error').text('');
                    }
                    // Validate the description field
                    var description = $('#description').val();
                    if (!description) {
                        $('#description_error').text('Description is required.');
                        isValid = false;
                    } else {
                        $('#description_error').text('');
                    }
                    // Validate the required proof answer field
                    var required_proof_answer = $('#required_proof_answer').val();
                    if (!required_proof_answer) {
                        $('#required_proof_answer_error').text('Required proof answer is required.');
                        isValid = false;
                    } else {
                        $('#required_proof_answer_error').text('');
                    }
                    // Validate the additional note field
                    var additional_note = $('#additional_note').val();
                    if (!additional_note) {
                        $('#additional_note_error').text('Additional note is required.');
                        isValid = false;
                    } else {
                        $('#additional_note_error').text('');
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

                // Validate the work needed field
                var work_needed = parseInt($('#work_needed').val());
                if (isNaN(work_needed)) {
                    $('#work_needed_error').text('Please enter how many workers are required.');
                    isValid = false;
                } else if (work_needed < 1) {
                    $('#work_needed_error').text('Work needed should be greater than or equal to 1.');
                    isValid = false;
                } else {
                    $('#work_needed_error').text('');
                }
                // Validate the earnings from work field
                var earnings_from_work = parseFloat($('#earnings_from_work').val());
                var minCharge = parseFloat($('#earnings_from_work').attr('min'));
                var maxCharge = parseFloat($('#earnings_from_work').attr('max'));
                if (isNaN(earnings_from_work)) {
                    $('#earnings_from_work_error').text('Please enter charges for each worker are required.');
                    isValid = false;
                } else if (earnings_from_work < minCharge) {
                    $('#earnings_from_work_error').text('Earnings from work should be greater than or equal to {{ get_site_settings("site_currency_symbol") }} ' + minCharge + '.');
                    isValid = false;
                } else if (earnings_from_work > maxCharge) {
                    $('#earnings_from_work_error').text('Earnings from work should be less than or equal to {{ get_site_settings("site_currency_symbol") }} ' + maxCharge + '.');
                    isValid = false;
                } else {
                    $('#earnings_from_work_error').text('');
                }
                // Validate the extra required proof photo field
                var required_proof_photo = parseInt($('#required_proof_photo').val());
                if (isNaN(required_proof_photo)) {
                    $('#required_proof_photo_error').text('Please enter how many additional required proof photo are required.');
                    isValid = false;
                } else if (required_proof_photo < 0) {
                    $('#required_proof_photo_error').text('Extra required proof photo should be greater than or equal to 0.');
                    isValid = false;
                } else {
                    $('#required_proof_photo_error').text('');
                }
                // Validate the boosted time field
                var boosted_time = parseInt($('#boosted_time').val());
                if (isNaN(boosted_time)) {
                    $('#boosted_time_error').text('Please select a boosted time.');
                    isValid = false;
                } else if (boosted_time < 0) {
                    $('#boosted_time_error').text('Boosted time should be greater than or equal to 0.');
                    isValid = false;
                } else {
                    $('#boosted_time_error').text('');
                }
                // Validate the work duration field
                var work_duration = parseInt($('#work_duration').val());
                if (isNaN(work_duration)) {
                    $('#work_duration_error').text('Please select a work duration.');
                    isValid = false;
                } else if (work_duration < 3) {
                    $('#work_duration_error').text('Work duration should be greater than or equal to 3.');
                    isValid = false;
                } else {
                    $('#work_duration_error').text('');
                }

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

                    // Disable the finish button and show the spinner
                    $('#wizard').find('.actions a[href="#finish"]').addClass('disabled');
                    $('#wizard').find('.actions a[href="#finish"]').hide();
                    $('#wizard').find('.actions ul').append('<strong class="btn btn-primary"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Editing...</strong>');
                }
            }
        });

        // thumbnail preview
        document.getElementById('thumbnailEdit').addEventListener('change', function() {
            const file = this.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file && allowedTypes.includes(file.type)) {
                if (file.size > maxSize) {
                    $('#thumbnailEditError').text('File size is too large. Max size is 2MB.');
                    this.value = ''; // Clear file input
                    // Hide preview image
                    $('#thumbnailPreviewEdit').hide();
                } else {
                    $('#thumbnailEditError').text('');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#thumbnailPreviewEdit').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                $('#thumbnailEditError').text('Please select a valid image file (jpeg, jpg, png).');
                this.value = ''; // Clear file input
                // Hide preview image
                $('#thumbnailPreviewEdit').hide();
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
                            options += '<label class="form-check-label" for="sub_category_' + sub_category.id + '">' + '<span class="badge">' + sub_category.name + '</span>' + '</label>';
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
                                                <span class="badge">${child_category.name}</span>
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

        // Add keyup event for work_needed, earnings_from_work, and required_proof_photo fields
        $('#work_needed, #earnings_from_work, #required_proof_photo').on('keyup', function() {
            calculateTotalTaskCharge();
        });

        // Calculate the total task charge
        function calculateTotalTaskCharge() {
            var work_needed = parseInt($('#work_needed').val()) || 0;
            var earnings_from_work = parseFloat($('#earnings_from_work').val()) || 0;

            var required_proof_photo = parseInt($('#required_proof_photo').val()) || 0;
            var required_proof_photo_charge = {{ get_default_settings('task_posting_additional_required_proof_photo_charge') }};

            var boosted_time = parseInt($('#boosted_time').val()) || 0;
            var boosted_time_charge = {{ get_default_settings('task_posting_boosted_time_charge') }};

            var work_duration = parseInt($('#work_duration').val()) || 0;
            var work_duration_charge = {{ get_default_settings('task_posting_additional_work_duration_charge') }};

            var task_posting_charge_percentage = {{ get_default_settings('task_posting_charge_percentage') }};
            var task_posting_min_budget = {{ get_default_settings('task_posting_min_budget') }};

            var total_proof_photo_charge = required_proof_photo_charge * (required_proof_photo > 1 ? required_proof_photo - 1 : 0);
            var total_boosted_time_charge = boosted_time_charge * (boosted_time / 15);
            var total_work_duration_charge = work_duration_charge * (work_duration - 3);

            var task_charge = (work_needed * earnings_from_work) + total_proof_photo_charge + total_boosted_time_charge + total_work_duration_charge;
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

        // Load the sub categories on page load
        var category_id = $('input[name="category_id"]:checked').val();
        loadSubCategories(category_id);
    });
</script>
@endsection

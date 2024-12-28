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
                                <strong>Warning!</strong> This task post has been rejected by the admin. Please check the rejection reason and edit the task post again. If you have any questions, please contact with us. <br>
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
                                <strong>1)</strong> Please fill out the form carefully. Once you submit the task, you can't edit, cancel or delete it.<br>
                                <strong>2)</strong> Your posted task will not approved if you violence's our policy or provide wrong information.<br>
                                <strong>3)</strong> After posted task you will see it in pending folder.<br>
                                <strong>4)</strong> If your posted task is approved by admin panel you will see it in running folder.<br>
                                <strong>5)</strong> Before your task expires, you will can boost, increase worker, and extend the duration of your running tasks.<br>
                                <strong>6)</strong> When you will boost your task, the task will showing at the top of the all posted task list.<br>
                                <strong>7)</strong> When your running task expires, it will be automatically removed from the running folder and moving to the completed folder, so the worker won't see your task in the Find Tasks option.<br>
                                <strong>8)</strong> If your posted task is rejected by admin panel you will get refund money and see it in rejected folder. You will be can edit and again resubmit before task duration expires.<br>
                                <strong>9</strong>) You will not get refund money before the task expires but if you cancel your running task then pending money will be refunded to you as per our policy wise and will see it in cancelled folder.<br>
                                <strong>10)</strong> You will can anytime pause your running tasks. If you pause your running task you will see it in paused folder and you can be resume the task from the paused folder before your task expires but if the task is paused by admin panel and want to resume the task need to contact us. When your task expires, it will be automatically removed from the paused folder and moving to the completed folder and get return pending money under our policy wise.<br>
                                <strong>11)</strong> You can be send bonuses, give ratings, approve and reject workers according to task proofs.<br>
                                <strong>12)</strong> If you do not approve or reject the Task Proof within {{ get_default_settings('posted_task_proof_submit_auto_approved_time') }} hours of submitting the worker Task Proof, the Task Proof will be automatically approved. If you reject the task proof, the worker can send us a request to review the task proof within {{ get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time') }} hours.<br>
                                <strong>13)</strong> Task submitting minimum rate is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_min_budget') }}.
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
                            <div class="mb-3">
                                <label for="required_proof_photo" class="form-label">
                                    Required Proof Photo <small class="text-info">* Optional </small>
                                </label>
                                <input type="number" class="form-control" name="required_proof_photo" id="required_proof_photo" value="{{ old('required_proof_photo', $postTask->required_proof_photo) }}" placeholder="Please enter how many required proof photo are required." min="0" max="10" required>
                                <small class="text-danger" id="required_proof_photo_error"></small>
                                <small class="text-info d-block">* Additional required proof photo charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_required_proof_photo_charge') }} per required proof photo.</small>
                                <small class="text-info d-block">* Note: You get 1 required proof photo for free. Max required proof photo is 10.</small>
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
                                    Thumbnail <small class="text-info">* Optional </small>
                                </label>
                                <input type="file" class="form-control" name="thumbnail" id="thumbnailEdit" accept=".jpg, .jpeg, .png">
                                <small id="thumbnailError" class="text-danger"></small>
                                <small class="text-info"> * Image format should be jpg, jpeg, png. * Image size should be less than 2MB.</small>
                                <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $postTask->thumbnail }}" id="thumbnailPreviewEdit" class="img-fluid my-2 d-block" style="max-height: 320px; display:none;" alt="Thumbnail">
                            </div>
                        </section>

                        <!-- Charge & Setting Section -->
                        <h2>Charge & Setting</h2>
                        <section>
                            <div class="row">
                                <div class="col-lg-3 col-12 mb-3">
                                    <label for="worker_needed" class="form-label">
                                        Worker Needed <small class="text-danger">* Required </small>
                                    </label>
                                    <input type="number" class="form-control" name="worker_needed" min="1" id="worker_needed" value="{{ old('worker_needed', $postTask->worker_needed) }}" placeholder="Please enter how many workers are required." required>
                                    <small class="text-danger" id="worker_needed_error"></small>
                                    <small class="text-info d-block">* Minimum worker needed is 1.</small>
                                </div>
                                <div class="col-lg-3 col-12 mb-3">
                                    <label for="income_of_each_worker" class="form-label">
                                        Income Of Each Worker <small class="text-danger">* Required </small>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="income_of_each_worker" id="income_of_each_worker" value="{{ old('income_of_each_worker', $postTask->income_of_each_worker) }}" placeholder="Please enter the charges for each worker." required>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                    </div>
                                    <small class="text-danger" id="income_of_each_worker_error"></small>
                                    <small class="text-info d-block">* Income of each worker should be within the min charge <strong id="min_task_charge">0</strong> and max charge <strong id="max_task_charge">0</strong>.</small>
                                </div>
                                <div class="col-lg-3 col-12 mb-3">
                                    <label for="task_cost" class="form-label">Task Cost</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="task_cost" readonly>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12 mb-3">
                                    <label for="site_charge" class="form-label">Site Charge</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="site_charge" readonly>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                    </div>
                                    <small class="text-info">* Site charge is {{ get_default_settings('task_posting_charge_percentage') }} % under worker needed and worker charge.</small>
                                </div>
                                <div class="col-lg-4 col-12 mb-3">
                                    <label for="boosting_time" class="form-label">
                                        Boosting Time <small class="text-info">* Optional </small>
                                    </label>
                                    <select class="form-select" name="boosting_time" id="boosting_time" required>
                                        <option value="0" {{ old('boosting_time', $postTask->boosting_time) == 0 ? 'selected' : '' }}>No Boost</option>
                                        <option value="15" {{ old('boosting_time', $postTask->boosting_time) == 15 ? 'selected' : '' }}>15 Minutes</option>
                                        <option value="30" {{ old('boosting_time', $postTask->boosting_time) == 30 ? 'selected' : '' }}>30 Minutes</option>
                                        <option value="45" {{ old('boosting_time', $postTask->boosting_time) == 45 ? 'selected' : '' }}>45 Minutes</option>
                                        <option value="60" {{ old('boosting_time', $postTask->boosting_time) == 60 ? 'selected' : '' }}>1 Hour</option>
                                        <option value="120" {{ old('boosting_time', $postTask->boosting_time) == 120 ? 'selected' : '' }}>2 Hours</option>
                                        <option value="180" {{ old('boosting_time', $postTask->boosting_time) == 180 ? 'selected' : '' }}>3 Hours</option>
                                        <option value="240" {{ old('boosting_time', $postTask->boosting_time) == 240 ? 'selected' : '' }}>4 Hours</option>
                                        <option value="300" {{ old('boosting_time', $postTask->boosting_time) == 300 ? 'selected' : '' }}>5 Hours</option>
                                        <option value="360" {{ old('boosting_time', $postTask->boosting_time) == 360 ? 'selected' : '' }}>6 Hours</option>
                                    </select>
                                    <small class="text-danger" id="work_duration_error"></small>
                                    <small class="text-info">* Every 15 minutes boost charges {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_boosting_time_charge') }}.</small>
                                    <br>
                                    <small class="text-info">* When the task is boosting, it will be shown at the top of the task list.</small>
                                </div>
                                <div class="col-lg-4 col-12 mb-3">
                                    <label for="work_duration" class="form-label">
                                        Work Duration <small class="text-info">* Optional </small>
                                    </label>
                                    <select class="form-select" name="work_duration" id="work_duration" required>
                                        <option value="3" {{ old('work_duration', $postTask->work_duration) == 3 ? 'selected' : '' }}>3 Days</option>
                                        <option value="4" {{ old('work_duration', $postTask->work_duration) == 4 ? 'selected' : '' }}>4 Days</option>
                                        <option value="5" {{ old('work_duration', $postTask->work_duration) == 5 ? 'selected' : '' }}>5 Days</option>
                                        <option value="6" {{ old('work_duration', $postTask->work_duration) == 6 ? 'selected' : '' }}>6 Days</option>
                                        <option value="7" {{ old('work_duration', $postTask->work_duration) == 7 ? 'selected' : '' }}>1 Week</option>
                                    </select>
                                    <small class="text-danger" id="work_duration_error"></small>
                                    <small class="text-info">* Additional work duration charge is {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_work_duration_charge') }} per day.</small>
                                    <br>
                                    <small class="text-info">* Note: You will get a default time of 3 days.</small>
                                    <br>
                                    <small class="text-info">* When work duration is over the task will be canceled automatically.</small>
                                </div>
                                <div class="col-lg-4 col-12 mb-3">
                                    <label for="total_task_charge" class="form-label">Total Task Cost</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="total_task_charge" readonly>
                                        <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                    </div>
                                    <small class="text-info">* Total task cost must be {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_min_budget') }}.</small>
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
        $('#taskForm').on('input', '#title, #description, #required_proof_answer, #required_proof_photo, #additional_note', function() {
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
            // Validate the extra required proof photo field
            var required_proof_photo = parseInt($('#required_proof_photo').val());
            if (required_proof_photo < 0 || required_proof_photo > 10) {
                $('#required_proof_photo_error').text('Required proof photo should be greater than or equal to 0 and less than or equal to 10.');
            } else {
                $('#required_proof_photo_error').text('');
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
        $('#taskForm').on('input', '#worker_needed, #income_of_each_worker', function() {
            // Validate the worker needed field
            var worker_needed = parseInt($('#worker_needed').val());
            if (isNaN(worker_needed)) {
                $('#worker_needed_error').text('Please enter how many workers are required.');
            } else if (worker_needed < 1) {
                $('#worker_needed_error').text('Worker needed should be greater than or equal to 1.');
            } else {
                $('#worker_needed_error').text('');
            }
            // Validate the income of each worker field
            var income_of_each_worker = parseFloat($('#income_of_each_worker').val());
            var minCharge = parseFloat($('#income_of_each_worker').attr('min'));
            var maxCharge = parseFloat($('#income_of_each_worker').attr('max'));
            if (isNaN(income_of_each_worker)) {
                $('#income_of_each_worker_error').text('Please enter charges for each worker are required.');
            } else if (income_of_each_worker < minCharge) {
                $('#income_of_each_worker_error').text('Income of each worker should be greater than or equal to {{ get_site_settings("site_currency_symbol") }} ' + minCharge + '.');
            } else if (income_of_each_worker > maxCharge) {
                $('#income_of_each_worker_error').text('Income of each worker should be less than or equal to {{ get_site_settings("site_currency_symbol") }} ' + maxCharge + '.');
            } else {
                $('#income_of_each_worker_error').text('');
            }
        });
        $('#taskForm').on('change', '#boosting_time, #work_duration', function() {
            // Validate the boosting time field
            var boosting_time = parseInt($('#boosting_time').val());
            if (isNaN(boosting_time)) {
                $('#boosting_time_error').text('Please select a boosting time.');
            } else if (boosting_time < 0) {
                $('#boosting_time_error').text('Boosting time should be greater than or equal to 0.');
            } else {
                $('#boosting_time_error').text('');
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
                    // Validate the extra required proof photo field
                    var required_proof_photo = parseInt($('#required_proof_photo').val());
                    if (required_proof_photo < 0 || required_proof_photo > 10) {
                        $('#required_proof_photo_error').text('Required proof photo should be greater than or equal to 0 and less than or equal to 10.');
                        isValid = false;
                    } else {
                        $('#required_proof_photo_error').text('');
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

                // Validate the worker needed field
                var worker_needed = parseInt($('#worker_needed').val());
                if (isNaN(worker_needed)) {
                    $('#worker_needed_error').text('Please enter how many workers are required.');
                    isValid = false;
                } else if (worker_needed < 1) {
                    $('#worker_needed_error').text('Worker needed should be greater than or equal to 1.');
                    isValid = false;
                } else {
                    $('#worker_needed_error').text('');
                }
                // Validate the income of each worker field
                var income_of_each_worker = parseFloat($('#income_of_each_worker').val());
                var minCharge = parseFloat($('#income_of_each_worker').attr('min'));
                var maxCharge = parseFloat($('#income_of_each_worker').attr('max'));
                if (isNaN(income_of_each_worker)) {
                    $('#income_of_each_worker_error').text('Please enter charges for each worker are required.');
                    isValid = false;
                } else if (income_of_each_worker < minCharge) {
                    $('#income_of_each_worker_error').text('Income of each worker should be greater than or equal to {{ get_site_settings("site_currency_symbol") }} ' + minCharge + '.');
                    isValid = false;
                } else if (income_of_each_worker > maxCharge) {
                    $('#income_of_each_worker_error').text('Income of each worker should be less than or equal to {{ get_site_settings("site_currency_symbol") }} ' + maxCharge + '.');
                    isValid = false;
                } else {
                    $('#income_of_each_worker_error').text('');
                }
                // Validate the boosting time field
                var boosting_time = parseInt($('#boosting_time').val());
                if (isNaN(boosting_time)) {
                    $('#boosting_time_error').text('Please select a boosting time.');
                    isValid = false;
                } else if (boosting_time < 0) {
                    $('#boosting_time_error').text('Boosting time should be greater than or equal to 0.');
                    isValid = false;
                } else {
                    $('#boosting_time_error').text('');
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
                    toastr.warning('Your total task cost must be ' + task_posting_min_budget + ' {{ get_site_settings('site_currency_symbol') }}.');
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
                        $('#income_of_each_worker').val(response.min_charge);
                        $('#income_of_each_worker').attr('min', response.min_charge);
                        $('#income_of_each_worker').attr('max', response.max_charge);
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

        // Add keyup event for worker_needed, income_of_each_worker, and required_proof_photo fields
        $('#worker_needed, #income_of_each_worker, #required_proof_photo').on('keyup', function() {
            calculateTotalTaskCharge();
        });

        // Calculate the total task cost
        function calculateTotalTaskCharge() {
            var task_posting_charge_percentage = {{ get_default_settings('task_posting_charge_percentage') }};
            var task_posting_min_budget = {{ get_default_settings('task_posting_min_budget') }};

            var worker_needed = parseInt($('#worker_needed').val()) || 0;
            var income_of_each_worker = parseFloat($('#income_of_each_worker').val()) || 0;

            var task_cost = (worker_needed * income_of_each_worker);
            $('#task_cost').val(task_cost.toFixed(2));

            var site_charge = ((task_cost * task_posting_charge_percentage) / 100);
            $('#site_charge').val(site_charge.toFixed(2));

            var required_proof_photo = parseInt($('#required_proof_photo').val()) || 0;
            var required_proof_photo_charge = {{ get_default_settings('task_posting_additional_required_proof_photo_charge') }};

            var boosting_time = parseInt($('#boosting_time').val()) || 0;
            var boosting_time_charge = {{ get_default_settings('task_posting_boosting_time_charge') }};

            var work_duration = parseInt($('#work_duration').val()) || 0;
            var work_duration_charge = {{ get_default_settings('task_posting_additional_work_duration_charge') }};

            var total_proof_photo_charge = required_proof_photo_charge * (required_proof_photo > 1 ? required_proof_photo - 1 : 0);
            var total_boosting_time_charge = boosting_time_charge * (boosting_time / 15);
            var total_work_duration_charge = work_duration_charge * (work_duration - 3);

            var total_task_charge = (task_cost + site_charge + total_proof_photo_charge + total_boosting_time_charge + total_work_duration_charge).toFixed(2);
            $('#total_task_charge').val(total_task_charge);

            // Calculate the increase charge
            var increase_charge = (task_posting_min_budget - total_task_charge).toFixed(2);
            if (increase_charge > 0) {
                $('#task_create_message').html('<strong>Posting this task need more  ' + ' {{ get_site_settings('site_currency_symbol') }} ' + increase_charge + ' total task cost. Then you can submit the task.</strong>');
                $('#task_create_message').addClass('alert-danger');
                $('#task_create_message').removeClass('alert-success');
            }else{
                $('#task_create_message').html('<strong>You can submit the task now by clicking the Edit button.</strong>');
                $('#task_create_message').removeClass('alert-danger');
                $('#task_create_message').addClass('alert-success');
            }

            // Validate the total task cost
            if (total_task_charge < task_posting_min_budget) {
                // desible the submit button
                $('#wizard').find('.actions a[href="#finish"]').addClass('disabled');
            } else {
                // enable the submit button
                $('#wizard').find('.actions a[href="#finish"]').removeClass('disabled');
            }
        }

        // Initialize the total task cost on page load
        calculateTotalTaskCharge();

        // Load the sub categories on page load
        var category_id = $('input[name="category_id"]:checked').val();
        loadSubCategories(category_id);
    });
</script>
@endsection

@section('script')
<script>
    $(document).ready(function() {
    $('#wizard').steps({
        headerTag: 'h2',
        bodyTag: 'section',
        transitionEffect: 'slideLeft',
        autoFocus: true,
        labels: {
            finish: "Submit"
        },
        onStepChanging: function(event, currentIndex, newIndex) {
            if (newIndex < currentIndex) return true;

            var form = $('#jobForm');
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
            var job_posting_min_budget = {{ get_default_settings('job_posting_min_budget') }};
            var user_deposit_balance = {{ Auth::user()->deposit_balance }};
            if(user_deposit_balance < job_posting_min_budget) {
                toastr.warning('Your balance is not enough to post a job. Please deposit now to post a job.');
                isValid = false;
            }else if ($('#total_job_cost').val() < job_posting_min_budget) {
                toastr.warning('Your total job cost must be ' + job_posting_min_budget + ' {{ get_site_settings('site_currency_symbol') }}.');
                isValid = false;
            }else{
                $('#jobForm').submit();
            }
        }
    });

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
            }
        });
    });

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
                        options += '<label class="form-check-label" for="child_category_' + child_category.id + '">' + '<span class="badge bg-primary">' + child_category.name + '</span>'  + '</label>';
                        options += '</div>';
                    });
                    $('#child-category-options').html(options);
                    $('#child-category-section').show();
                } else {
                    $('#child-category-section').hide();
                    $('#child-category-options').html('');
                }
                var workerChargeInput = $('#worker_charge');
                workerChargeInput.attr('min', response.job_post_charge.working_min_charges);
                workerChargeInput.attr('max', response.job_post_charge.working_max_charges);
                workerChargeInput.val(response.job_post_charge.working_min_charges);

                $('#min_charge').text(response.job_post_charge.working_min_charge + ' {{ get_site_settings('site_currency_symbol') }}');
                $('#max_charge').text(response.job_post_charge.working_max_charge + ' {{ get_site_settings('site_currency_symbol') }}');
            }
        });
    });

    $(document).on('change', 'input[name="child_category_id"]', function() {
        var category_id = $('input[name="category_id"]:checked').val();
        var sub_category_id = $('input[name="sub_category_id"]:checked').val();
        var child_category_id = $(this).val();
        if (child_category_id) {
            $.ajax({
                url: "{{ route('post_job.get_job_post_charge') }}",
                type: 'GET',
                data: {
                    category_id: category_id,
                    sub_category_id: sub_category_id,
                    child_category_id: child_category_id
                },
                success: function(response) {
                    var workerChargeInput = $('#worker_charge');
                    workerChargeInput.attr('min', response.job_post_charge.working_min_charge);
                    workerChargeInput.attr('max', response.job_post_charge.working_max_charge);
                    workerChargeInput.val(response.job_post_charge.working_min_charge);

                    $('#min_charge').text(response.job_post_charge.working_min_charge + ' {{ get_site_settings('site_currency_symbol') }}');
                    $('#max_charge').text(response.job_post_charge.working_max_charge + ' {{ get_site_settings('site_currency_symbol') }}');
                }
            });
        }
    });

    $('#jobForm').on('change', 'input, select', function() {
        calculateTotalJobCost();
    });

    function calculateTotalJobCost() {
        var need_worker = parseInt($('#need_worker').val()) || 0;
        var worker_charge = parseFloat($('#worker_charge').val()) || 0;
        var extra_screenshots = parseInt($('#extra_screenshots').val()) || 0;
        var job_boosted_time = parseInt($('#job_boosted_time').val()) || 0;
        var screenshot_charge = {{ get_default_settings('job_posting_additional_screenshot_charge') }};
        var boosted_time_charge = {{ get_default_settings('job_posting_boosted_time_charge') }};
        var job_posting_charge_percentage = {{ get_default_settings('job_posting_charge_percentage') }};

        var job_cost = (need_worker * worker_charge) +
            (extra_screenshots * screenshot_charge) +
            ((job_boosted_time / 15) * boosted_time_charge);

        $('#job_cost').val(job_cost.toFixed(2));
        var site_charge = (job_cost * job_posting_charge_percentage / 100);
        $('#site_charge').val(site_charge.toFixed(2));
        $('#total_job_cost').val((job_cost + site_charge).toFixed(2));
    }

    function validateInputFields() {
        let isValid = true;

        // Validate need_worker
        let needWorker = parseInt($('#need_worker').val());
        if (isNaN(needWorker) || needWorker < 1) {
            $('#need_worker').addClass('is-invalid');
            isValid = false;
        } else {
            $('#need_worker').removeClass('is-invalid');
        }

        // Validate worker_charge
        let workerCharge = parseFloat($('#worker_charge').val());
        let minCharge = parseFloat($('#worker_charge').attr('min'));
        if (isNaN(workerCharge) || workerCharge < minCharge) {
            $('#worker_charge').addClass('is-invalid');
            isValid = false;
        } else {
            $('#worker_charge').removeClass('is-invalid');
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

    // Call validateInputFields on form submit and on input change
    $('#jobForm').on('submit', function(event) {
        if (!validateInputFields()) {
            event.preventDefault();
            return false;
        }
    });

    $('#jobForm').on('change', 'input', function() {
        validateInputFields();
        calculateTotalJobCost();  // Recalculate job cost after validation
    });

});
</script>
@endsection

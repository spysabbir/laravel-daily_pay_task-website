@extends('layouts.template_master')

@section('title', 'Work Details')

@section('content')
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Work Details</h3>
                <div class="d-flex">
                    <h3 class="mx-2">Need Worker: <strong class="badge bg-primary">{{ $workDetails->need_worker }}</strong></h3>
                    <h3 class="mx-2">Worker Charge: <strong class="badge bg-primary">{{ $workDetails->worker_charge }} {{ get_site_settings('site_currency_symbol') }}</strong></h3>
                </div>
            </div>
            <div class="card-body">
                <h3 class="mb-3">Work Title: {{ $workDetails->title }}</h3>
                <img src="{{ asset('uploads/job_thumbnail_photo') }}/{{ $workDetails->thumbnail }}" alt="Thumbnail image for {{ $workDetails->title }}" class="img-fluid">
                <div class="my-3">
                    <p>Category: {{ $workDetails->category->name }}</p>
                    <p>Running Day: {{ $workDetails->running_day }}</p>
                </div>
                <div class="my-2 border p-2 rounded">
                    <h3>Description</h3>
                    <p>{{ $workDetails->description }}</p>
                </div>
                <div class="my-2 border p-2 rounded">
                    <h3>Required Proof</h3>
                    <p>{{ $workDetails->required_proof }}</p>
                </div>
                <hr>
                <div class="my-2 border p-3 rounded bg-dark">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($workProofExists)
                    <div class="alert alert-warning">
                        <strong>Proof already submitted!</strong>
                    </div>
                    @else
                    <form id="workForm" action="{{ route('work.proof.submit', encrypt($workDetails->id)) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="proof_answer" class="form-label">Proof Answer <small class="text-danger">* Required </small></label>
                            <textarea class="form-control" id="proof_answer" name="proof_answer" rows="5" placeholder="Write your answer here">{{ old('proof_answer') }}</textarea>
                            <span class="text-danger error-message" id="proof_answer_error"></span>
                            @if ($errors->has('proof_answer'))
                                <span class="text-danger">{{ $errors->first('proof_answer') }}</span>
                            @endif
                        </div>

                        @for ($i = 0; $i < $workDetails->extra_screenshots + 1; $i++)
                            <div class="mb-3">
                                <label for="proof_photo_{{ $i }}" class="form-label">Proof Photo {{ $i + 1 }} <small class="text-danger">* Required </small></label>
                                <input class="form-control" type="file" id="proof_photo_{{ $i }}" name="proof_photos[]" accept=".jpg, .jpeg, .png">
                                <small class="text-info">* Only jpeg, jpg, png files are allowed. File size must be less than 2 MB.</small>
                                <span class="text-danger error-message d-block" id="proof_photo_{{ $i }}_error"></span>
                                <img src="" alt="Proof Photo {{ $i + 1 }}" class="img-fluid" id="proof_photo_preview_{{ $i }}" style="display: none;">
                                @if ($errors->has('proof_photos.' . $i))
                                    <span class="text-danger">{{ $errors->first('proof_photos.' . $i) }}</span>
                                @endif
                            </div>
                        @endfor

                        @if ($errors->has('proof_photos'))
                            <div class="mb-3">
                                <span class="text-danger">{{ $errors->first('proof_photos') }}</span>
                            </div>
                        @endif

                        <div class="my-2 d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Find Works</h3>
            </div>
            <div class="card-body">
                <!-- Additional content for finding works can go here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Client-side validation
        $('#workForm').submit(function(event) {
            let isValid = true;
            $('.error-message').text(''); // Clear previous error messages

            // Validate proof answer
            if ($('#proof_answer').val().trim() === '') {
                $('#proof_answer_error').text('Proof answer is required.');
                isValid = false;
            }else{

            }

            // Validate proof photos (if required)
            @for ($i = 0; $i < $workDetails->extra_screenshots + 1; $i++)
                if ($('#proof_photo_{{ $i }}').val() === '') {
                    $('#proof_photo_{{ $i }}_error').text('Proof photo {{ $i + 1 }} is required.');
                    isValid = false;
                }else{
                    let fileExtension = ['jpeg', 'jpg', 'png'];
                    if ($.inArray($('#proof_photo_{{ $i }}').val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        $('#proof_photo_{{ $i }}_error').text('Only jpeg, jpg, png files are allowed.');
                        isValid = false;
                    }
                    if ($('#proof_photo_{{ $i }}')[0].files[0].size > 2097152) {
                        $('#proof_photo_{{ $i }}_error').text('File size must be less than 2 MB.');
                        isValid = false;
                    }
                }
            @endfor

            if(isValid){
                {{ $proofCount = App\Models\JobProof::where('job_id', $workDetails->id)->where('status', '!=', 'Rejected')->count() }}
                if ({{ $proofCount }} >= {{ $workDetails->need_worker }}) {
                    toastr.warning('Proof submission limit reached!');
                    isValid = false;
                }
            }

            if (!isValid) {
                event.preventDefault(); // Prevent form submission if validation fails
            }

        });

        // Preview proof photos
        @for ($i = 0; $i < $workDetails->extra_screenshots + 1; $i++)
            $('#proof_photo_{{ $i }}').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#proof_photo_preview_{{ $i }}').attr('src', e.target.result).css('display', 'block');
                }
                reader.readAsDataURL(this.files[0]);
            });
        @endfor
    });
</script>
@endsection

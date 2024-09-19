@vite('resources/js/app.js')
@extends('layouts.template_master')

@section('title', 'Support')

@section('content')
<div class="row chat-wrapper justify-content-center">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="chat-content">
                    <div class="chat-header border-bottom pb-2">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <i data-feather="corner-up-left" id="backToChatList" class="icon-lg me-2 ms-n2 text-muted d-lg-none"></i>
                                <figure class="mb-0 me-2">
                                    <img src="{{ asset('uploads/profile_photo') }}/{{ Auth::user()->profile_photo }}" class="img-sm rounded-circle" alt="image">
                                </figure>
                                <div>
                                    <p>{{ Auth::user()->name }}</p>
                                    <p class="text-muted tx-13">
                                        Id: {{ Auth::user()->id }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-body">
                        <ul class="messages" id="chatBox">
                            @forelse ($supports as $support)
                            @if ($support->sender_id  === Auth::user()->id)
                            <li class="message-item friend">
                                <img src="{{ asset('uploads/profile_photo') }}/{{ $support->sender->profile_photo }}" class="img-xs rounded-circle" alt="avatar">
                                <div class="content">
                                    <div class="message">
                                        <div class="bubble">
                                            <p>{{ $support->message }}</p>
                                        </div>
                                        <span>{{ $support->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </li>
                            @endif
                            @if ($support->receiver_id  === Auth::user()->id)
                            <li class="message-item me">
                                <img src="{{ asset('uploads/profile_photo') }}/{{ $support->receiver->profile_photo }}" class="img-xs rounded-circle" alt="avatar">
                                <div class="content">
                                    <div class="message">
                                        <div class="bubble">
                                            <p>{{ $support->message }}</p>
                                        </div>
                                        <span>{{ $support->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </li>
                            @endif
                            @empty
                                <div class="alert alert-primary text-center" id="noMessage">
                                    <strong>No message found!</strong>
                                </div>
                            @endforelse
                        </ul>
                    </div>
                    <div class="chat-footer border-top pt-2">
                        <form enctype="multipart/form-data" id="sendMessageForm">
                            @csrf
                            <div class="d-flex">
                                <div class="mx-1 py-3">
                                    <input type="file" name="photo" id="fileInput" style="display:none;" accept=".jpg, .jpeg, .png">
                                    <button type="button" id="fileBtn" class="btn border btn-icon rounded-circle" data-bs-toggle="tooltip" title="Attach files">
                                        <i data-feather="paperclip" class="text-muted"></i>
                                    </button>
                                </div>
                                <div class="mx-1" id="imagePreviewContainer" style="display: none;">
                                    <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100px; max-height: 100px;">
                                </div>
                                <div class="mx-1 input-group">
                                    <textarea name="message" class="form-control " placeholder="Type a message"></textarea>
                                </div>
                                <div class="mx-1 py-3">
                                    <button type="submit" class="btn btn-primary btn-icon rounded-circle">
                                        <i data-feather="send"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="text-danger error-text message_error"></span>
                                <span class="text-danger error-text photo_error"></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Trigger the file input when the button is clicked
    document.getElementById('fileBtn').addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });

    // Display the selected image in the preview area
    document.getElementById('fileInput').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Set the image source to the file's data URL
                document.getElementById('imagePreview').src = e.target.result;
                // Show the preview container
                document.getElementById('imagePreviewContainer').style.display = 'block';
            }
            reader.readAsDataURL(file); // Read the file as a Data URL
        }
    });

    // Send message to the server using AJAX request and append the message to the chatBox
    $('#sendMessageForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            url: '{{ route("support.send-message") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
            success: function(response) {
                if (response.status === 400) {
                    $.each(response.error, function(prefix, val){
                        $('span.'+prefix+'_error').text(val[0]);
                    })
                } else {
                    $('#noMessage').addClass('d-none');
                    var profile_photo = '{{ Auth::user()->profile_photo }}';
                    var created_at = new Date(response.support.created_at).toLocaleString();
                    var support_photo = response.support.photo ? `<img src="{{ asset('uploads/support_photo') }}/${response.support.photo}" alt="image" style="max-width: 100px; max-height: 100px;">` : '';

                    if (response.support.sender_id === {{ Auth::user()->id }}) {
                        $('#chatBox').append(`
                            <li class="message-item friend">
                                <img src="{{ asset('uploads/profile_photo') }}/${profile_photo}" class="img-xs rounded-circle" alt="avatar">
                                <div class="content">
                                    <div class="message">
                                        <div class="bubble">
                                            <p class='mb-2'>${response.support.message}</p>
                                            ${support_photo}
                                        </div>
                                        <span>${created_at}</span>
                                    </div>
                                </div>
                            </li>
                        `);
                    }

                    $('textarea[name="message"]').val('');
                    $('#fileInput').val('');
                    $('#imagePreview').attr('src', '');
                    $('#imagePreviewContainer').hide();

                    // Scroll to the bottom of the chatBox
                    $('.chat-body').animate({ scrollTop: $('.chat-body').prop('scrollHeight') }, 1000);
                }
            },
        });
    });

    // Listen for new messages using Echo
    window.onload = () => {
        window.Echo.channel('support')
        .listen('SupportEvent', function(data) {
            if (data.support.receiver_id === {{ Auth::user()->id }}) {
                $('#chatBox').append(`
                    <li class="message-item me">
                        <img src="{{ asset('uploads/profile_photo') }}/${data.support.receiver_photo}" class="img-xs rounded-circle" alt="avatar">
                        <div class="content">
                            <div class="message">
                                <div class="bubble">
                                    <p class='mb-2'>${data.support.message}</p>
                                    ${data.support.photo ? `<img src="{{ asset('uploads/support_photo') }}/${data.support.photo}" alt="image" style="max-width: 100px; max-height: 100px;">` : ''}
                                </div>
                                <span>${data.support.created_at}</span>
                            </div>
                        </div>
                    </li>
                `);
            }

            // Scroll to the bottom of the chatBox
            $('.chat-body').animate({ scrollTop: $('.chat-body').prop('scrollHeight') }, 1000);
        });
    }
</script>
@endsection

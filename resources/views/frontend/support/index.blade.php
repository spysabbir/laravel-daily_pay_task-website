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
                            <!-- Messages will be appended here -->
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
                                <br>
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
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file && allowedTypes.includes(file.type)) {
            if (file.size > maxSize) {
                $('span.photo_error').text('File size is too large. Max size is 2MB.');
                this.value = ''; // Clear file input
                // Hide preview image
                document.getElementById('imagePreviewContainer').style.display = 'none';
            } else {
                $('span.photo_error').text('');
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreviewContainer').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        } else {
            $('span.photo_error').text('Please select a valid image file (jpeg, jpg, png).');
            this.value = ''; // Clear file input
            // Hide preview image
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }
    });

    // Scroll to the bottom of the chatBox
    $('.chat-body').scrollTop($('.chat-body')[0].scrollHeight);

    // Real-time validation for message input
    $('textarea[name="message"]').on('input', function() {
        var message = $(this).val().trim();
        if (message.length > 0) {
            $('span.message_error').text(''); // Remove error message when input is valid
        }else{
            $('span.message_error').text('Message is required!');
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
                    var created_at = response.support.created_at;
                    var support_photo = response.support.photo ? `<img src="{{ asset('uploads/support_photo') }}/${response.support.photo}" alt="image" style="max-width: 100px; max-height: 100px;">` : '';

                    if (response.support.sender_id === {{ Auth::user()->id }}) {
                        $('#chatBox').append(`
                            <li class="message-item friend">
                                <img src="{{ asset('uploads/profile_photo') }}/${profile_photo}" class="img-xs rounded-circle" alt="avatar">
                                <div class="content">
                                    <div class="message">
                                        <div class="bubble">
                                            <p class='mb-2'>${response.support.message}</p>
                                            <a href="{{ asset('uploads/support_photo') }}/${response.support.photo}" target="_blank">${support_photo}</a>
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
                                    ${data.support.photo ? `<a href="{{ asset('uploads/support_photo') }}/${data.support.photo}" target="_blank"><img src="{{ asset('uploads/support_photo') }}/${data.support.photo}" alt="image" style="max-width: 100px; max-height: 100px;"></a>` : ''}
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

    $.ajax({
            url: '{{ route("support.get-message") }}',
            method: 'GET',
            success: function(data) {
                if (data.supports.length > 0) {
                    data.supports.forEach(function(support) {
                        var profile_photo = support.sender_id === {{ Auth::user()->id }} ? support.sender_photo : support.receiver_photo;
                        var support_photo = support.photo ? `<img src="{{ asset('uploads/support_photo') }}/${support.photo}" alt="image" style="max-width: 100px; max-height: 100px;">` : '';
                        var timeDisplay = support.created_at;

                        $('#chatBox').append(`
                            <li class="message-item ${support.sender_id === {{ Auth::user()->id }} ? 'friend' : 'me'}">
                                <img src="{{ asset('uploads/profile_photo') }}/${profile_photo}" class="img-xs rounded-circle" alt="avatar">
                                <div class="content">
                                    <div class="message">
                                        <div class="bubble">
                                            <p class='mb-2'>${support.message}</p>
                                            ${support_photo ? `<a href="{{ asset('uploads/support_photo') }}/${support.photo}" target="_blank">${support_photo}</a>` : ''}
                                        </div>
                                        <span>${timeDisplay}</span>
                                    </div>
                                </div>
                            </li>
                        `);
                    });

                    // Scroll to the bottom of the chatBox
                    $('.chat-body').scrollTop($('.chat-body')[0].scrollHeight);
                }
                else {
                    $('#chatBox').append(`
                        <div class="alert alert-primary text-center" id="noMessage">
                            <strong>No message found!</strong>
                        </div>
                    `);
                }
            }
        });
</script>
@endsection

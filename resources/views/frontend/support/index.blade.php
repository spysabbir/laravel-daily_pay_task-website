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
                                    <button type="button" id="fileBtn" class="btn border btn-icon rounded-circle">
                                        <i data-feather="paperclip" class="text-muted"></i>
                                    </button>
                                </div>
                                <div class="mx-1" id="imagePreviewContainer" style="display: none;">
                                    <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100px; max-height: 100px;">
                                </div>
                                <div class="mx-1 input-group">
                                    <textarea name="message" class="form-control" placeholder="Type a message" rows="1"></textarea>
                                </div>
                                <div class="mx-1 py-3">
                                    <button type="submit" class="btn btn-primary btn-icon rounded-circle">
                                        <i data-feather="send"></i>
                                    </button>
                                </div>
                            </div>
                            <span class="text-danger error-text photo_error"></span>
                            <span class="d-block text-danger error-text validator_alert_error"></span>
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

    $('textarea[name="message"]').on('input', function () {
        validateInputs();
    });

    // Monitor file selection for the photo input
    $('#fileInput').on('change', function () {
        const file = this.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file) {
            if (!allowedTypes.includes(file.type)) {
                $('span.photo_error').text('Invalid file type. Only jpeg, jpg, and png are allowed.');
                this.value = ''; // Clear file input
            } else if (file.size > maxSize) {
                $('span.photo_error').text('File size is too large. Max size is 2MB.');
                this.value = ''; // Clear file input
            } else {
                $('span.photo_error').text(''); // Clear errors if valid
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreviewContainer').show();
                };
                reader.readAsDataURL(file);
            }
        } else {
            $('#imagePreviewContainer').hide();
            $('#imagePreview').attr('src', '');
            $('span.photo_error').text('');
        }

        validateInputs();
    });

    // Function to validate both inputs in real-time
    function validateInputs() {
        const message = $('textarea[name="message"]').val().trim();
        const photo = $('#fileInput')[0].files.length > 0;

        if (!message && !photo) {
            $('span.validator_alert_error').text('Either a message or a photo is required.');
        } else {
            $('span.validator_alert_error').text('');
        }
    }

    // Function to scroll chat box to the bottom
    function scrollToBottom() {
        $('.chat-body').animate({ scrollTop: $('.chat-body')[0].scrollHeight }, 1000);
    }

    // Fetch all messages from the server
    function fetchMessages() {
        $.ajax({
            url: '{{ route("support.get-message") }}',
            method: 'GET',
            success: function(data) {
                $('#chatBox').html(''); // Clear the chat box before appending new messages

                // Load messages
                if (data.supports.length > 0) {
                    data.supports.forEach(function(support) {
                        var profile_photo = support.sender_id === {{ Auth::user()->id }} ? support.sender_photo : support.receiver_photo;
                        var support_photo = support.photo ? `<img src="{{ asset('uploads/support_photo') }}/${support.photo}" alt="image" style="max-width: 100px; max-height: 100px;">` : '';
                        var timeDisplay = support.created_at;

                        var sanitizedMessage = support.message.replace(/\n/g, '<br>');

                        $('#chatBox').append(`
                            <li class="message-item ${support.sender_id === {{ Auth::user()->id }} ? 'friend' : 'me'}">
                                <img src="{{ asset('uploads/profile_photo') }}/${profile_photo}" class="img-xs rounded-circle" alt="avatar">
                                <div class="content">
                                    <div class="message">
                                        <div class="bubble">
                                            <p>${support.message ? sanitizedMessage: ''}</p>
                                            ${support_photo ? `<a href="{{ asset('uploads/support_photo') }}/${support.photo}" target="_blank">${support_photo}</a>` : ''}
                                        </div>
                                        <span>${support.status == 'Read' ? '<i class="fas fa-check-double text-success"></i>' : '<i class="fas fa-check"></i>'} ${timeDisplay}</span>
                                    </div>
                                </div>
                            </li>
                        `);
                    });

                    // Scroll to the bottom of the chatBox
                    scrollToBottom();
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
    }
    fetchMessages();

    // Send message to the server using AJAX request and append the message to the chatBox
    $('#sendMessageForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        var submitButton = $(this).find("button[type='submit']");
        submitButton.prop("disabled", true).html('<i class="spinner-border spinner-border-sm"></i>');

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
                    fetchMessages();

                    $('textarea[name="message"]').val('');
                    $('#fileInput').val('');
                    $('#imagePreview').attr('src', '');
                    $('#imagePreviewContainer').hide();
                }
            },
            complete: function() {
                submitButton.prop("disabled", false).html('<i class="fas fa-paper-plane"></i>');
            }
        });
    });

    // Listen for new messages using Echo
    window.onload = () => {
        window.Echo.channel('support')
        .listen('SupportEvent', function(data) {
            var sanitizedMessage = data.support.message.replace(/\n/g, '<br>');
            if (data.support.receiver_id === {{ Auth::user()->id }}) {
                $('#chatBox').append(`
                    <li class="message-item me">
                        <img src="{{ asset('uploads/profile_photo') }}/${data.support.receiver_photo}" class="img-xs rounded-circle" alt="avatar">
                        <div class="content">
                            <div class="message">
                                <div class="bubble">
                                    <p>${data.support.message ? sanitizedMessage : ''}</p>
                                    ${data.support.photo ? `<a href="{{ asset('uploads/support_photo') }}/${data.support.photo}" target="_blank"><img src="{{ asset('uploads/support_photo') }}/${data.support.photo}" alt="image" style="max-width: 100px; max-height: 100px;"></a>` : ''}
                                </div>
                                <span>${data.support.status == 'Read' ? '<i class="fas fa-check-double text-success"></i>' : '<i class="fas fa-check"></i>'} ${data.support.created_at}</span>
                            </div>
                        </div>
                    </li>
                `);
            }

            // Scroll to the bottom of the chatBox
            scrollToBottom();
        });
    }

    setInterval(fetchMessages, 100000);
</script>
@endsection

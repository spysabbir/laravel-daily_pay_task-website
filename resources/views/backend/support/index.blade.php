@vite('resources/js/app.js')
@extends('layouts.template_master')

@section('title', 'Support')

@section('content')
<div class="row chat-wrapper">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row position-relative">
                    <!-- Sidebar: Support Users List -->
                    <div class="col-lg-4 chat-aside border-end-lg">
                        <div class="aside-content">
                            <div class="aside-header">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-2">
                                    <div class="d-flex align-items-center">
                                        <figure class="me-2 mb-0">
                                            <img src="{{ asset('uploads/profile_photo') }}/{{ Auth::user()->profile_photo }}" class="img-sm rounded-circle" alt="profile">
                                            <div class="status online"></div>
                                        </figure>
                                        <div>
                                            <h6>{{ Auth::user()->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!-- Search Form -->
                                <form class="search-form">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i data-feather="search" class="cursor-pointer"></i>
                                        </span>
                                        <input type="text" class="form-control" id="searchForm" placeholder="Search here...">
                                    </div>
                                </form>
                            </div>

                            <!-- Support Users Tab -->
                            <div class="aside-body">
                                <ul class="nav nav-tabs nav-fill mt-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="chats-tab" data-bs-toggle="tab" data-bs-target="#chats" role="tab" aria-controls="chats" aria-selected="true">
                                            <i data-feather="message-square"></i> Supports
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">
                                    <div class="tab-pane fade show active" id="chats" role="tabpanel">
                                        <p class="text-muted mb-1">Recent Supports</p>
                                        <ul class="list-unstyled chat-list px-1">
                                            @foreach ($supportUsers as $user)
                                                @php
                                                    $support = App\Models\Support::where('sender_id', $user->id)->latest()->first();
                                                    $supportsCount = App\Models\Support::where('sender_id', $user->id)->count();
                                                @endphp
                                                <li class="chat-item">
                                                    <a href="javascript:;" class="d-flex align-items-center select-user" data-id="{{ $user->id }}">
                                                        <figure class="mb-0 me-2">
                                                            <img src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" class="img-xs rounded-circle" alt="user">
                                                            <div class="status online"></div>
                                                        </figure>
                                                        <div class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                            <div>
                                                                <p class="text-body fw-bolder">{{ $user->name }}</p>
                                                                <p class="text-muted">{{ $support->message ?? 'No messages yet' }}</p>
                                                            </div>
                                                            <div class="badge bg-primary ms-auto">{{ $supportsCount }}</div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Chat Area -->
                    <div class="col-lg-8 chat-content">
                        <div class="chat-header border-bottom pb-2">
                            <div class="d-flex align-items-center">
                                <figure class="mb-0 me-2">
                                    <img src="" class="img-sm rounded-circle" alt="User Image">
                                </figure>
                                <div>
                                    <p class="user-name"></p>
                                    <p class="text-muted tx-13"></p>
                                </div>
                            </div>
                        </div>

                        <div class="chat-body">
                            <ul class="messages">
                                <!-- Messages will load here -->
                            </ul>
                        </div>

                        <!-- Message Form -->
                        <div class="chat-footer">
                            <form enctype="multipart/form-data" id="sendMessageReplyForm">
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
                                        <textarea name="message" class="form-control" placeholder="Type a message"></textarea>
                                    </div>
                                    <div class="mx-1 py-3">
                                        <button type="submit" class="btn btn-primary btn-icon rounded-circle">
                                            <i data-feather="send"></i>
                                        </button>
                                    </div>
                                </div>
                                <span class="text-danger error-text message_error"></span>
                                <span class="text-danger error-text photo_error"></span>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Trigger file input
    $('#fileBtn').click(function() {
        $('#fileInput').click();
    });

    // Image Preview
    $('#fileInput').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewContainer').show();
            };
            reader.readAsDataURL(file);
        }
    });

    // AJAX: Send message
    $('#sendMessageReplyForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let userId = 2; // Replace with dynamic user ID as needed
        let url = "{{ route('backend.support.send-message.reply', ':id') }}".replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('span.error-text').text('');
            },
            success: function(response) {
                if (response.status === 400) {
                    $.each(response.errors, function(prefix, val) {
                        $('span.' + prefix + '_error').text(val[0]);
                    });
                } else {
                    const messageHtml = `
                        <li class="message-item me">
                            <img src="{{ asset('uploads/profile_photo') }}/${response.support.sender_profile}" class="img-xs rounded-circle" alt="avatar">
                            <div class="content">
                                <div class="message">
                                    <div class="bubble">
                                        <p>${response.support.message}</p>
                                        ${response.support.photo ? `<img src="{{ asset('uploads/support_photo') }}/${response.support.photo}" style="max-width: 100px;">` : ''}
                                    </div>
                                    <span>${new Date(response.support.created_at).toLocaleString()}</span>
                                </div>
                            </div>
                        </li>`;

                    $('.messages').append(messageHtml);

                    // Clear form
                    $('textarea[name="message"]').val('');
                    $('#fileInput').val('');
                    $('#imagePreviewContainer').hide();
                    $('#imagePreview').attr('src', '');
                }
            }
        });
    });

    // Load User's Chat
    $('.select-user').on('click', function() {
        const userId = $(this).data('id');
        const url = "{{ route('backend.get.user.support.list', ':id') }}".replace(':id', userId);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // Update chat header
                $('.chat-header img').attr('src', response.profile_photo);
                $('.chat-header .user-name').text(response.name);
                $('.chat-header .text-muted').text(response.role);

                // Load messages
                const messageList = $('.messages');
                messageList.empty();
                response.messages.forEach(function(message) {
                    const messageItem = `
                        <li class="message-item ${message.sender_type}">
                            <img src="${message.profile_photo}" class="img-xs rounded-circle" alt="avatar">
                            <div class="content">
                                <div class="message">
                                    <div class="bubble">
                                        <p>${message.text}</p>
                                    </div>
                                    <span>${message.time}</span>
                                </div>
                            </div>
                        </li>`;
                    messageList.append(messageItem);
                });
            }
        });
    });

    // Listen for real-time messages with Echo
    window.Echo.channel('support')
        .listen('SupportEvent', function(data) {
            if (data.support.receiver_id === 2) { // Replace 2 with dynamic user ID
                const messageHtml = `
                    <li class="message-item">
                        <img src="{{ asset('uploads/profile_photo') }}/${data.support.sender_profile}" class="img-xs rounded-circle" alt="avatar">
                        <div class="content">
                            <div class="message">
                                <div class="bubble">
                                    <p>${data.support.message}</p>
                                    ${data.support.photo ? `<img src="{{ asset('uploads/support_photo') }}/${data.support.photo}" style="max-width: 100px;">` : ''}
                                </div>
                                <span>${new Date(data.support.created_at).toLocaleString()}</span>
                            </div>
                        </div>
                    </li>`;

                $('.messages').append(messageHtml);
            }
        });
</script>
@endsection

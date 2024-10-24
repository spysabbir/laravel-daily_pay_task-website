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
                                            <p class="text-muted tx-13">
                                                Id: {{ Auth::user()->id }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Search Form -->
                                {{-- <form class="search-form">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i data-feather="search" class="cursor-pointer"></i>
                                        </span>
                                        <input type="text" class="form-control" id="searchForm" placeholder="Search here...">
                                    </div>
                                </form> --}}
                            </div>

                            <!-- Support Users Tab -->
                            <div class="aside-body">
                                <ul class="nav nav-tabs nav-fill mt-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="chats-tab" data-bs-toggle="tab" data-bs-target="#chats" role="tab" aria-controls="chats" aria-selected="true">
                                            <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center justify-content-center">
                                                <i data-feather="message-square" class="icon-sm me-sm-2 me-lg-0 me-xl-2 mb-md-1 mb-xl-0"></i>
                                                <p class="d-none d-sm-block">Supports</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts" role="tab" aria-controls="contacts" aria-selected="false">
                                        <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center justify-content-center">
                                            <i data-feather="users" class="icon-sm me-sm-2 me-lg-0 me-xl-2 mb-md-1 mb-xl-0"></i>
                                            <p class="d-none d-sm-block">User</p>
                                        </div>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">
                                    <div class="tab-pane fade show active" id="chats" role="tabpanel">
                                        <p class="text-muted mb-1 text-center border">Recent Supports</p>
                                        <ul class="list-unstyled chat-list px-1">
                                            @foreach ($supportUsers as $user)
                                                @php
                                                    $support = App\Models\Support::where('sender_id', $user->id)->latest()->first();
                                                    $supportsCount = App\Models\Support::where('sender_id', $user->id)->where('status', 'Unread')->count();
                                                @endphp
                                                <li class="chat-item">
                                                    <a href="javascript:;" class="d-flex align-items-center select-user" data-id="{{ $user->id }}">
                                                        <figure class="mb-0 me-2">
                                                            <img src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" class="img-xs rounded-circle" alt="user">
                                                            <div class="status {{ \Carbon\Carbon::parse($user->last_login_at)->diffInMinutes(now()) <= 5 ? 'online' : 'offline' }}"></div>
                                                        </figure>
                                                        <div class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                            <div>
                                                                <p class="text-body fw-bolder">Id: {{ $user->id }}, Name: {{ $user->name }}</p>
                                                                <p class="text-muted">{{ $support->message ?? 'No messages yet' }}</p>
                                                            </div>
                                                            <div class="d-flex flex-column align-items-end">
                                                                <p class="text-muted tx-13 mb-1">{{ $support->created_at->diffForHumans() }}</p>
                                                                <div class="badge rounded-pill bg-primary ms-auto">{{ $supportsCount }}</div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="tab-pane fade" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
                                        <p class="text-muted mb-1 text-center border">All User</p>
                                        <ul class="list-unstyled chat-list px-1">
                                            @foreach ($users as $user)
                                            <li class="chat-item pe-1">
                                                <a href="javascript:;" class="d-flex align-items-center select-user" data-id="{{ $user->id }}">
                                                    <figure class="mb-0 me-2">
                                                        <img src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" class="img-xs rounded-circle" alt="user">
                                                        <div class="status {{ \Carbon\Carbon::parse($user->last_login_at)->diffInMinutes(now()) <= 5 ? 'online' : 'offline' }}"></div>
                                                    </figure>
                                                    <div class="d-flex align-items-center justify-content-between flex-grow-1 border-bottom">
                                                        <div>
                                                            <p class="text-body">Id: {{ $user->id }}, Name: {{ $user->name }}</p>
                                                            <div class="d-flex align-items-center">
                                                                <p class="text-muted tx-13">{{  Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-end text-body">
                                                            <i data-feather="message-square" class="icon-md text-primary me-2"></i>
                                                        </div>
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
                    <div class="col-lg-8 chat-content d-none">
                        <!-- User ID -->
                        <input type="hidden" id="userId" value="">
                        <!-- Chat Header -->
                        <div class="chat-header border-bottom pb-2">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i data-feather="corner-up-left" id="backToChatList" class="icon-lg me-2 ms-n2 text-muted d-lg-none"></i>
                                    <figure class="mb-0 me-2">
                                        <img src="{{ asset('uploads/profile_photo/default_profile_photo.png') }}" class="img-sm rounded-circle" alt="User Image">
                                        <div class="status active_status"></div>
                                    </figure>
                                    <div>
                                        <p class="user-name">User</p>
                                        <p class="text-muted tx-13"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Chat Body -->
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
                                <br>
                                <span class="text-danger error-text photo_error"></span>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-8 empty-chat">
                        <div class="alert alert-info text-center" role="alert">
                            <strong>Choose a user to start chat</strong>
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
        }
    });

    // Real-time validation for message input
    $('textarea[name="message"]').on('input', function() {
        var message = $(this).val().trim();
        if (message.length > 0) {
            $('span.message_error').text(''); // Remove error message when input is valid
        }else{
            $('span.message_error').text('Message is required!');
        }
    });

    // Use event delegation to handle click on dynamically loaded .select-user
    $(document).on('click', '.select-user', function() {
        const userId = $(this).data('id');
        $('#userId').val(userId);

        if (userId) {
            $('.chat-content').removeClass('d-none');
            $('.empty-chat').addClass('d-none');
        } else {
            $('.chat-content').addClass('d-none');
            $('.empty-chat').removeClass('d-none');
        }

        const url = "{{ route('backend.get.support.users', ':id') }}".replace(':id', userId);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // Update chat header
                $('.chat-header img').attr('src', response.profile_photo);
                $('.chat-header .user-name').text(response.name);
                $('.chat-header .text-muted').text(response.last_active);
                $('.chat-header .active_status').removeClass('offline').addClass(response.active_status);

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
                                        <p>${message.message}</p>
                                    </div>
                                    <span>${message.created_at}</span>
                                </div>
                            </div>
                        </li>`;
                    messageList.append(messageItem);

                });

                // Scroll to bottom
                // $('.chat-body').animate({ scrollTop: $('.chat-body').prop('scrollHeight') }, 1000);

                // Show last message
                $('.chat-body').scrollTop($('.chat-body')[0].scrollHeight);


                // Refresh recent support list
                loadRecentSupports();

                // Clear form
                $('textarea[name="message"]').val('');
                $('#fileInput').val('');
                $('#imagePreviewContainer').hide();
            }
        });
    });

    // AJAX: Send message
    $('#sendMessageReplyForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let userId = $('#userId').val();
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
                    var profile_photo = "{{ Auth::user()->profile_photo }}";
                    var created_at = response.support.created_at;
                    const messageHtml = `
                        <li class="message-item me">
                            <img src="{{ asset('uploads/profile_photo') }}/${profile_photo}" class="img-xs rounded-circle" alt="avatar">
                            <div class="content">
                                <div class="message">
                                    <div class="bubble">
                                        <p>${response.support.message}</p>
                                        ${response.support.photo ? `<img src="{{ asset('uploads/support_photo') }}/${response.support.photo}" style="max-width: 100px;">` : ''}
                                    </div>
                                    <span>${created_at}</span>
                                </div>
                            </div>
                        </li>`;

                    $('.messages').append(messageHtml);

                    // Clear form
                    $('textarea[name="message"]').val('');
                    $('#fileInput').val('');
                    $('#imagePreviewContainer').hide();
                    $('#imagePreview').attr('src', '');

                    // Scroll to bottom
                    $('.chat-body').animate({ scrollTop: $('.chat-body').prop('scrollHeight') }, 1000);

                    // Refresh recent support list
                    loadRecentSupports();
                }
            }
        });
    });

    // Listen for real-time messages with Echo
    window.onload = () => {
        window.Echo.channel('support')
        .listen('SupportEvent', function(data) {
            if (data.support.receiver_id === {{ Auth::user()->id }}) {
                $('.messages').append(`
                    <li class="message-item friend">
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

                // Scroll to bottom
                $('.chat-body').animate({ scrollTop: $('.chat-body').prop('scrollHeight') }, 1000);

                // Refresh recent support list
                loadRecentSupports();
            }
        });
    };

    // Function to load recent supports
    function loadRecentSupports() {
        $.ajax({
            url: "{{ route('backend.get.latest.support.users') }}",
            method: 'GET',
            success: function(response) {
                const supportList = $('#chats .chat-list');
                supportList.empty(); // Clear existing list
                response.supportUsers.forEach(function(user) {
                    console.log(user);

                    const supportItem = `
                        <li class="chat-item">
                            <a href="javascript:;" class="d-flex align-items-center select-user" data-id="${user.id}">
                                <figure class="mb-0 me-2">
                                    <img src="{{ asset('uploads/profile_photo') }}/${user.profile_photo}" class="img-xs rounded-circle" alt="user">
                                    <div class="status ${user.active_status}"></div>
                                </figure>
                                <div class="d-flex justify-content-between flex-grow-1 border-bottom">
                                    <div>
                                        <p class="text-body fw-bolder">Id: ${user.id}, Name: ${user.name}</p>
                                        <p class="text-muted">${user.message}</p>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <p class="text-muted tx-13 mb-1">${user.send_at}</p>
                                        <div class="badge rounded-pill bg-primary ms-auto">${user.support_count}</div>
                                    </div>
                                </div>
                            </a>
                        </li>`;
                    supportList.append(supportItem);
                });
            }
        });
    }
</script>
@endsection

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
                                                Role: {{ Auth::user()->roles->pluck('name')->join(', ') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Search Form -->
                                <div class="search-form" id="searchUserIdForm">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchUserId" placeholder="Search user id ...">
                                        <button type="button" class="input-group-text">
                                            <i data-feather="search" class="cursor-pointer"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Support Users Tab -->
                            <div class="aside-body">
                                <ul class="nav nav-tabs nav-fill mt-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="unread-supports-users-tab" data-bs-toggle="tab" data-bs-target="#unread-supports-users" role="tab" aria-controls="unread-supports-users" aria-selected="true">
                                            <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center justify-content-center">
                                                <i data-feather="message-square" class="icon-sm me-sm-2 me-lg-0 me-xl-2 mb-md-1 mb-xl-0"></i>
                                                <p class="d-none d-sm-block">Unread Supports Users</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="all-users-tab" data-bs-toggle="tab" data-bs-target="#all-users" role="tab" aria-controls="all-users" aria-selected="false">
                                            <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center justify-content-center">
                                                <i data-feather="users" class="icon-sm me-sm-2 me-lg-0 me-xl-2 mb-md-1 mb-xl-0"></i>
                                                <p class="d-none d-sm-block">All Users</p>
                                            </div>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">
                                    <div class="tab-pane fade show active" id="unread-supports-users" role="tabpanel">
                                        <p class="text-muted mb-1 text-center border">Unread Supports Users</p>
                                        <ul class="list-unstyled chat-list px-1">
                                            <!-- Users will load here -->
                                        </ul>
                                    </div>
                                    <div class="tab-pane fade" id="all-users" role="tabpanel" aria-labelledby="all-users-tab">
                                        <p class="text-muted mb-1 text-center border">All Users</p>
                                        <ul class="list-unstyled chat-list px-1">
                                            <!-- Users will load here -->
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
                        @can('support.reply')
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
                        @endcan
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

    function fetchMessages(userId) {
        const url = "{{ route('backend.get.user.supports', ':id') }}".replace(':id', userId);

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

                if (response.messages.length > 0) {
                    response.messages.forEach(function(message) {
                        var sanitizedMessage = message.message.replace(/\n/g, '<br>');
                        const messageItem = `
                            <li class="message-item ${message.sender_type}">
                                <img src="${message.profile_photo}" class="img-xs rounded-circle" alt="avatar">
                                <div class="content">
                                    <div class="message">
                                        <div class="bubble">
                                            <p>${message.message ? sanitizedMessage : ''}</p>
                                            ${message.photo ? `<a href="{{ asset('uploads/support_photo') }}/${message.photo}" target="_blank"><img src="{{ asset('uploads/support_photo') }}/${message.photo}" alt="image" style="max-width: 100px; max-height: 100px;"></a>` : ''}
                                        </div>
                                        <span>${message.status == 'Read' ? '<i class="fas fa-check-double text-success"></i>' : '<i class="fas fa-check"></i>'} ${message.created_at}</span>
                                    </div>
                                </div>
                            </li>`;
                        messageList.append(messageItem);
                    });

                    // Scroll to last message
                    scrollToBottom();
                } else {
                    // Scroll to top
                    $('.chat-body').animate({ scrollTop: 0 }, 1000);
                    // Display "No messages yet" when chat is empty
                    messageList.append(`
                        <div class="alert alert-primary text-center" id="noMessage">
                            <strong>No message found!</strong>
                        </div>
                    `);
                }

                // Refresh recent support list
                fetchSupportUsers('unread-supports-users-tab'); // Fetch unread users for unreadTab
                fetchSupportUsers('all-users-tab'); // Fetch all users for allUsersTab

                // Clear form
                $('textarea[name="message"]').val('');
                $('#fileInput').val('');
                $('#imagePreview').attr('src', '');
                $('#imagePreviewContainer').hide();
            },
        });
    }

    // AJAX: Load chat messages
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

        fetchMessages(userId);
    });

    setInterval(function () {
        const userId = $('#userId').val() || 1;
        fetchMessages(userId);
    }, 100000)


    // AJAX: Send message
    $('#sendMessageReplyForm').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let userId = $('#userId').val();
        let url = "{{ route('backend.support.send-message.reply', ':id') }}".replace(':id', userId);

        var submitButton = $(this).find("button[type='submit']");
        submitButton.prop("disabled", true).html('<i class="spinner-border spinner-border-sm"></i>');

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
                    fetchMessages(userId);

                    $('textarea[name="message"]').val('');
                    $('#fileInput').val('');
                    $('#imagePreview').attr('src', '');
                    $('#imagePreviewContainer').hide();


                    // Refresh recent support list
                    fetchSupportUsers('unread-supports-users-tab'); // Fetch unread users for unreadTab
                    fetchSupportUsers('all-users-tab'); // Fetch all users for allUsersTab
                }
            },
            complete: function() {
                submitButton.prop("disabled", false).html('<i class="fas fa-paper-plane"></i>');
            }
        });
    });

    // Listen for real-time messages with Echo
    window.onload = () => {
        window.Echo.channel('support')
        .listen('SupportEvent', function(data) {
            var sanitizedMessage = data.support.message.replace(/\n/g, '<br>');
            if (data.support.receiver_id === {{ Auth::user()->id }}) {
                $('.messages').append(`
                    <li class="message-item friend">
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

                // Scroll to bottom
                scrollToBottom();

                // Refresh recent support list
                fetchSupportUsers('unread-supports-users-tab'); // Fetch unread users for unreadTab
                fetchSupportUsers('all-users-tab'); // Fetch all users for allUsersTab
            }
        });
    };

    // Function to fetch and render users for a given tab
    function fetchSupportUsers(tab, searchUserId = '') {
        const url = "{{ route('backend.get.search.support.user') }}";

        // Prepare data object dynamically
        const data = { tab: tab };
        if (searchUserId.trim() !== '') {
            data.searchUserId = searchUserId;
        }

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            success: function (response) {
                const unreadTab = $('#unread-supports-users .chat-list');
                const allUsersTab = $('#all-users .chat-list');

                // Clear the relevant tab's list
                if (response.tab === 'unread-supports-users-tab') {
                    unreadTab.empty();
                } else if (response.tab === 'all-users-tab') {
                    allUsersTab.empty();
                }

                // Append users to the respective tab
                if (response.supportUsers && response.supportUsers.length > 0) {
                    response.supportUsers.forEach(user => {
                        const targetTab = response.tab === 'unread-supports-users-tab' ? unreadTab : allUsersTab;
                        appendUser(user, targetTab);
                    });
                } else {
                    const targetTab = response.tab === 'unread-supports-users-tab' ? unreadTab : allUsersTab;
                    handleNoUserFound(targetTab);
                }
            },
            error: function () {
                const unreadTab = $('#unread-supports-users .chat-list');
                const allUsersTab = $('#all-users .chat-list');

                if (tab === 'unread-supports-users-tab') {
                    handleNoUserFound(unreadTab);
                } else {
                    handleNoUserFound(allUsersTab);
                }
            }
        });
    }

    // Append user to the list
    function appendUser(user, targetList) {
        const userItem = `
            <li class="chat-item">
                <a href="javascript:;" class="d-flex align-items-center select-user" data-id="${user.id}">
                    <figure class="mb-0 me-2">
                        <img src="{{ asset('uploads/profile_photo') }}/${user.profile_photo}" class="img-xs rounded-circle" alt="user">
                        <div class="status ${user.active_status}"></div>
                    </figure>
                    <div class="d-flex justify-content-between flex-grow-1 border-bottom">
                        <div>
                            <p class="text-body fw-bolder">Id: ${user.id}, Name: ${user.name}, Status: ${user.status}</p>
                            <p class="text-muted">${user.message}</p>
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <p class="text-muted tx-13 mb-1">${user.send_at}</p>
                            <div class="badge rounded-pill bg-primary ms-auto">${user.support_count}</div>
                        </div>
                    </div>
                </a>
            </li>`;
        targetList.append(userItem);
    }

    // Handle "No users found" message
    function handleNoUserFound(targetList) {
        targetList.empty();
        const noUserMessage = `
            <li class="chat-item my-2">
                <div class="d-flex align-items-center justify-content-center">
                    <p class="text-info">No users found</p>
                </div>
            </li>`;
        targetList.append(noUserMessage);
    }

    // Fetch initial data for both tabs
    fetchSupportUsers('unread-supports-users-tab'); // Fetch unread users for unreadTab
    fetchSupportUsers('all-users-tab'); // Fetch all users for allUsersTab

    // Handle search input
    $('#searchUserId').on('input', function () {
        const searchUserId = $(this).val(); // Get search input
        const activeTab = $('.nav-tabs .active').attr('id'); // Get active tab ID
        fetchSupportUsers(activeTab, searchUserId);
    });

    // Handle tab change
    $('.nav-tabs a').on('click', function (e) {
        e.preventDefault();

        const searchUserId = $('#searchUserId').val();
        const activeTab = $(this).attr('id');

        fetchSupportUsers(activeTab, searchUserId);
    });
</script>
@endsection

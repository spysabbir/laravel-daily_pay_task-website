@extends('layouts.template_master')

@section('title', 'Support')

@section('content')
<div class="row chat-wrapper">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row position-relative">
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

                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <form class="search-form">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                        <i data-feather="search" class="cursor-pointer"></i>
                                        </span>
                                        <input type="text" class="form-control" id="searchForm" placeholder="Search here...">
                                    </div>
                                </form>
                            </div>
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
                                            <p class="d-none d-sm-block">Contacts</p>
                                        </div>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-3">
                                    <div class="tab-pane fade show active" id="chats" role="tabpanel" aria-labelledby="chats-tab">
                                        <div>
                                            <p class="text-muted mb-1">Recent Supports</p>
                                            <ul class="list-unstyled chat-list px-1">
                                                @foreach ($supportUsers as $user)
                                                @php
                                                    $supports = App\Models\Support::where('sender_id', $user->id)->get();
                                                    $support = App\Models\Support::where('sender_id', $user->id)->orderBy('created_at', 'desc')->first();
                                                @endphp
                                                <li class="chat-item pe-1">
                                                    <a href="javascript:;" class="d-flex align-items-center select-user" data-id="{{ $user->id }}">
                                                        <figure class="mb-0 me-2">
                                                            <img src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" class="img-xs rounded-circle" alt="user">
                                                            <div class="status online"></div>
                                                        </figure>
                                                        <div class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                            <div>
                                                                <p class="text-body fw-bolder">{{ $user->name }}</p>
                                                                <p class="text-muted tx-13">{{ $support->message }}</p>
                                                            </div>
                                                            <div class="d-flex flex-column align-items-end">
                                                                <p class="text-muted tx-13 mb-1">{{ $support->created_at->diffForHumans() }}</p>
                                                                <div class="badge rounded-pill bg-primary ms-auto">{{ $supports->count() }}</div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
                                        <p class="text-muted mb-1">Contacts</p>
                                        <ul class="list-unstyled chat-list px-1">
                                            @foreach ($users as $user)
                                            <li class="chat-item pe-1">
                                                <a href="javascript:;" class="d-flex align-items-center">
                                                    <figure class="mb-0 me-2">
                                                        <img src="https://via.placeholder.com/37x37" class="img-xs rounded-circle" alt="user">
                                                        <div class="status offline"></div>
                                                    </figure>
                                                    <div class="d-flex align-items-center justify-content-between flex-grow-1 border-bottom">
                                                        <div>
                                                            <p class="text-body">{{ $user->name }}</p>
                                                            <div class="d-flex align-items-center">
                                                                <p class="text-muted tx-13">Front-end Developer</p>
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
                    <div class="col-lg-8 chat-content">
                        <div class="chat-header border-bottom pb-2">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i data-feather="corner-up-left" id="backToChatList" class="icon-lg me-2 ms-n2 text-muted d-lg-none"></i>
                                    <figure class="mb-0 me-2">
                                        <img src="https://via.placeholder.com/43x43" class="img-sm rounded-circle" alt="image">
                                        <div class="status online"></div>
                                        <div class="status online"></div>
                                    </figure>
                                    <div>
                                        <p>Mariana Zenha</p>
                                        <p class="text-muted tx-13">Front-end Developer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chat-body">
                            <ul class="messages">
                                <li class="message-item friend">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                            </div>
                                            <span>8:12 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item me">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry printing and typesetting industry.</p>
                                            </div>
                                        </div>
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum.</p>
                                            </div>
                                            <span>8:13 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item friend">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                            </div>
                                            <span>8:15 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item me">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry printing and typesetting industry.</p>
                                            </div>
                                            <span>8:15 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item friend">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                            </div>
                                            <span>8:12 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item me">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry printing and typesetting industry.</p>
                                            </div>
                                        </div>
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum.</p>
                                            </div>
                                            <span>8:13 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item friend">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                            </div>
                                            <span>8:15 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item me">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry printing and typesetting industry.</p>
                                            </div>
                                            <span>8:15 PM</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="chat-footer">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="d-flex">
                                    <div class="mx-1 py-3">
                                        <input type="file" name="image" id="fileInput" style="display:none;" accept=".jpg, .jpeg, .png">
                                        <button type="button" id="fileBtn" class="btn border btn-icon rounded-circle" data-bs-toggle="tooltip" title="Attatch files">
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
</script>
@endsection

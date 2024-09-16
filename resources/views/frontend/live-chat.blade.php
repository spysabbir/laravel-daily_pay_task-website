@extends('layouts.frontend')

@section('title', 'Live Chat')

@section('content')
<div class="pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Live Chat</h2>
        </div>
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="card msg_card">
                    <div class="card-header msg_head">
                        <div class="d-flex bd-highlight">
                            <div class="img_cont">
                                <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">
                                <span class="online_icon"></span>
                            </div>
                            <div class="user_info">
                                <span>Chat</span>
                                <p>0 Messages</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body msg_card_body">
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg">
                            </div>
                            <div class="msg_cotainer">
                                Hi, how are you samim?
                                <span class="msg_time">8:40 AM, Today</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-4">
                            <div class="msg_cotainer_send">
                                Hi Khalid i am good tnx how about you?
                                <span class="msg_time_send">8:55 AM, Today</span>
                            </div>
                            <div class="img_cont_msg">
                                <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <div class="input-group-append">
                                    <input type="file" name="image" id="fileInput" style="display:none;" accept="image/*">
                                    <button type="button" class="input-group-text" id="fileBtn">
                                        <i class='bx bxs-file-image'></i>
                                    </button>
                                </div>
                                <div id="imagePreviewContainer" style="display: none;" class="mx-2 rounded">
                                    <img id="imagePreview" src="" alt="Image Preview" style="max-width: 90px; max-height: 90px;">
                                </div>
                                <textarea name="message" class="form-control type_msg mx-2" placeholder="Type your message..."></textarea>
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text send_btn">
                                        <i class='bx bxs-send'></i>
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
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection

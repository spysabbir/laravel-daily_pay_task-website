<div class="border border-primary p-3 rounded">

    <h4>User Id: {{ $notification->notifiable_id }}</h4>
    <h4>User Name: {{ $notification->notifiable->name }}</h4>
    <h4>User Type: {{ $notification->notifiable->user_type == 'Frontend' ? 'User' : 'Employee' }}</h4>
    <hr>
    <h5>Title: {{ $notification->data['title'] }}</h5>
    <p>Message: {{ $notification->data['message'] }}</p>
    <hr>
    <h5>Send at: {{ $notification->created_at->format('j-M, Y h:i:s A') }}</h5>

</div>

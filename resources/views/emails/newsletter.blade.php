<!DOCTYPE html>
<html>
<head>
    <title>{{ $newsletter->subject }}</title>
</head>
<body>
    <h1>Newsletter</h1>
    <p>Hi User,</p>
    <p>Here is the latest newsletter from our company:</p>


    <h3>{{ $newsletter->subject }}</h3>
    <p>{{ $newsletter->content }}</p>

    <hr>

    <p style="margin-bottom: 10px">Sent at: {{ date('j M, Y H:i:s A', strtotime($newsletter->created_at)) }}</p>

    <p>Thank you for subscribing to our newsletter!</p>
    <p>If you wish to unsubscribe, please click <a href="{{ route('unsubscribe', encrypt($userId)) }}" target="_blank">here</a>.</p>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>{{ $newsletter->subject }}</title>
</head>
<body>
    <h1>{{ $newsletter->subject }}</h1>
    <p>{{ $newsletter->content }}</p>
    <p>Sent at: {{ $newsletter->sent_at }}</p>
</body>
</html>

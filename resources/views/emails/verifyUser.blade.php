<!DOCTYPE html>
<html>
<head>
    <title>Welcome email</title>
</head>
<body>
    <h2>Welcome to the Laravel-Blog, {{ $user->name }}</h2>
    <p>Please, click on the link below to verify email address and complete registration</p>
    <a href="{{ url('user/verify', $user->verifyUser->token) }}">Verify email</a>
</body>
</html>
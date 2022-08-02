<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body style="margin: 100px;">

    <h3>Hi.. <span style="color:cornflowerblue">{{ $userName }}</span></h3>
    <h4>You have requested to reset your password</h4>
    <hr>
    <h1 style="font-weight: bold;color:cornflowerblue "><a
            href="http://127.0.0.1:8000/api/reset-password/{{ $token }}">Click Here to
            Reset Password</a></h1>
    <br><br>
    <span style="">Team <span style="font-weight: bold;color:cornflowerblue">Auth-api</span></span>
</body>

</html>

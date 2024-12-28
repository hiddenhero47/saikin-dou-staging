<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Verification</title>
    <style>
        body{
            text-align: center;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        div{
            margin-top: 5%;
        }
        div p{
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div>
        @if ($status === 'success')
            <img src="{{url('assets/images/icons/success.png')}}" width="460" height="345">
            <p>Successfully verified your email.</p>
        @elseif($status === 'failure')
            <img src="{{url('assets/images/icons/failure.png')}}" width="460" height="345">
            <p>Failed to verify your email, your link may have expired. Please try again.</p>
        @endif
    </div>
</body>
</html>
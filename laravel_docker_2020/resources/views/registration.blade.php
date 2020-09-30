<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Все для примера по работы бэка по этому некоторые вещи опущены</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <form action="/auth/registration" method="POST">
            <div>
                <label for="login">login</label>
                <input id="login" name="login" type="text" class="@error('login') is-invalid @enderror">
            </div>
            <div>
                <label for="pass">pass</label>
                <input id="password" name="password" type="password" class="@error('password') is-invalid @enderror">
            </div>
            <div>
                <label for="email">email</label>
                <input id="email" name="email" type="email" class="@error('email') is-invalid @enderror">
            </div>
            <button type="submit">register</button>
        </form>
    </div>
</div>
</body>
</html>

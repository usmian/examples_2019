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
            @csrf
            <div class="form-group row">
                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Псевдоним') }}</label>
                @if ($errors->has('name'))
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </div>
                @endif
                <div class="col-md-6">
                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name" value="{{ old('name') }}" autofocus>
                </div>
                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Пароль') }}</label>
                @if ($errors->has('password'))
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong></div>
                @endif
                <div class="col-md-6">
                    <input id="password" type="text"
                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                           name="password">
                </div>
                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                @if ($errors->has('email'))
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong></div>
                @endif
                <div class="col-md-6">
                    <input id="email" type="email"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           name="email">
                </div>
                <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Телефон') }}</label>
                @if ($errors->has('phone'))
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('phone') }}</strong></div>
                @endif
                <div class="col-md-6">
                    <input id="phone" type="text"
                           class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                           name="phone">
                </div>
                <button type="submit" style="margin: 5px">register</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

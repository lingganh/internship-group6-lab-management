<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ config('app.name', 'Hệ thống quản lý lịch phòng lab') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/login.png') }}">
    @include('includes.style')
    {{ $custom_css ?? '' }}
    @include('includes.script')
    {{ $custom_js ?? '' }}
</head>

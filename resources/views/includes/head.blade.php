<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.LM = {
            userId: @json(auth()->id()),
            reverb: {
                key: @json(env('REVERB_APP_KEY')),
                host: @json(env('REVERB_HOST', '127.0.0.1')),
                port: @json((int) env('REVERB_PORT', 8080)),
                scheme: @json(env('REVERB_SCHEME', 'http')),
            }
        };
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <title>{{ config('app.name', 'Hệ thống quản lý lịch phòng lab') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/login.png') }}">
{{--    @vite(['resources/css/app.css', 'resources/js/app.js'])--}}
    @include('includes.style')
    {{ $custom_css ?? '' }}
    @include('includes.script')
    {{ $custom_js ?? '' }}
    <link rel="stylesheet" href="{{ asset('assets/css/notification/custom.css') }}">
    @livewireStyles
</head>

<!-- Global stylesheets -->
<link href="{{ asset('assets/fonts/inter/inter.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/icons/phosphor/styles.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/all.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/noty/noty.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/style.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
<!-- /global stylesheets -->
@livewireStyles
{{--@vite('resources/css/app.scss')--}}
{{--@vite('resources/css/auth.scss')--}}
<!-- Scripts -->
{{--<!-- Css custom -->--}}
@yield('style_custom')
{{--<!-- /Css custom  -->--}}

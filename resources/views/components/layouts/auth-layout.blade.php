<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('includes.head')
<style>
    .login-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .login-wrapper .card-body {
        padding: 20px;
    }

    .login-wrapper .card {
        margin-bottom: 0;
    }

    .login-wrapper .login-image-wrapper {
        padding: 20px 20px 0 20px;
        text-align: center;
    }
    .login-wrapper .login-image {
        width: 400px;
    }
    .login-wrapper .login-image-wrapper .line {
        margin: 10px auto 20px auto;
        background: #e5e5e5;
        width: 50px;
        height: 1px;
    }
    .login-wrapper .login-form {
        margin: 0 auto;
    }

    @media screen and (max-width: 678px) {
        .login-wrapper {
            max-width: 420px;
        }

        .login-wrapper .login-image {
            width: 250px;
        }
    }

</style>
<body>


<!-- Page content -->
<div class="page-content">

    {{--    <!-- Main sidebar -->--}}
    {{--    @include('includes.sidebar')--}}
    {{--    <!-- /main sidebar -->--}}


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Inner content -->
        <div class="content-inner">

            <!-- Content area -->
            {{ $slot }}
            <!-- /content area -->

            <!-- Footer -->
            @include('includes.footer')
            <!-- /footer -->

        </div>
        <!-- /inner content -->

    </div>
    <!-- /main content -->

</div>
<!-- /page content -->



</body>

</html>

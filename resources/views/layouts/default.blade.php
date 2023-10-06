<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no"
        name="viewport">
    <title>@yield('title') Helfanza Nanda Alfara</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- @stack('style') --}}
    @yield('baseStyles')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    <!-- Start GA -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>
    <!-- END GA -->

    <script>
        const BASE_URL = "{{ asset('') }}";
        const APP_URL = "{{ env('APP_URL') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const GLOBAL_ROLE_NAME = "{{ auth()->check() ? auth()->user()->getRoleNames()[0] : null }}";
    </script>
</head>
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <!-- Header -->
            {{-- @include('components.header')

            <!-- Sidebar -->
            @include('components.sidebar') --}}

            <!-- Content -->
            @yield('body')

            <!-- Footer -->
            {{-- @include('components.footer') --}}
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>

    <script src="{{ asset('js/stisla.js') }}"></script>

    {{-- @stack('scripts') --}}
	@yield('baseScripts')

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/alert.js') }}"></script>
    <script src="{{ asset('js/cookie.js') }}"></script>

    <script>
        $(document).ready(function () {
            let user = getCookie("user");
            let role = getCookie("role");
            if (user && role) {
                user = JSON.parse(user)
                $(".txt-name").text(user.name)
                $(".txt-role").text(role)
            }
        });

        $(document).on('click', '.btn-logout', async function (e) {
            e.preventDefault()
            const isConfirmed = await showPopupConfirmation('are you sure you want to logout?', 'Logout')
            if (isConfirmed) {
                const url = `${APP_URL}/api/logout`;
                const method = "POST";
                const response = await request(url, method);
                if (response.status) {
                    eraseCookie("token");
                    eraseCookie("expires_in");
                    eraseCookie("user");
                    eraseCookie("role");
                    showAlertOnSubmit(response, '', '', `${APP_URL}/login`);

                }
            }
        })
    </script>
</body>

</html>


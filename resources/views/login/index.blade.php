@extends('layouts.guest')

@section('content')
<div class="card card-primary " style="width: 31rem">
    <div class="card-header">
        <h4>Login</h4>
    </div>

    <div class="card-body">
        <form id="main-form">
            <x-form.input label="email" name="email"/>
            <x-form.input type="password" label="password" name="password"/>


            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                    Login
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).on('submit', '#main-form', async function (e) {
            e.preventDefault()
            const url = `${APP_URL}/api/login`;
            const method = "POST";
            const params = Object.fromEntries(new FormData(e.target).entries());


            const response = await request(url, method, params);
            if (response.status) {

                setCookie("token", response.data.access_token);
                setCookie("expires_in", response.data.expires_in);
                setCookie("user", JSON.stringify(response.data.user));
                setCookie("role", response.data.role);
                // console.log('REDIRECT KESINI HARUSNYA : ', APP_URL);
                // console.log('REDIRECT KESINI HARUSNYA 2: ', `${APP_URL}`);
                showAlertOnSubmit(response, '', '', BASE_URL);
            }
        })
    </script>
@endpush

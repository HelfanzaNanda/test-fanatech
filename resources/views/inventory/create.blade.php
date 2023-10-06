@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <x-breadcrumb :menus="$menus" :title="$title" />

        <div class="section-body">

            <div class="card">
                <form id="main-form">
                    <div class="card-header">
                        <h4>{{ $title }}</h4>
                    </div>
                    @include('inventory.form');
                    <div class="card-footer text-right">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary mr-2">Back</a>
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@endsection

@push('scripts')
    <script>
        $(document).on('submit', '#main-form', async function (e) {
            e.preventDefault()
            const url = `${APP_URL}/api/inventory`;
            const method = "POST";
            // const params = {};
            const params = Object.fromEntries(new FormData(e.target).entries());


            const response = await request(url, method, params);
            if (response.status) {
                showAlertOnSubmit(response, '', '', `${APP_URL}/inventory`);
            }
        })
    </script>
@endpush

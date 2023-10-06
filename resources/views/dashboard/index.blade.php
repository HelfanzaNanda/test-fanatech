@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">

        <x-breadcrumb :menus="$menus" :title="$title" />

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="mt-0 mb-3 d-flex justify-content-between align-items-center">
                        <div class="section-title ">{{ $title }}</div>
                    </div>
                    <h2>Welcome <span class="txt-name">{name}</span> </h2>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let user = getCookie("user");
            user = JSON.parse(user)
            $(".txt-name").text(user.name)
        });
    </script>
@endpush

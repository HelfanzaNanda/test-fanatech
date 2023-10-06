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
                        <a href="{{ route('inventory.index') }}" class="btn-back btn btn-secondary mr-2">Back</a>
                        <button class="btn-submit btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@endsection

@push('scripts')
    <script>
        const id = "{{ $id }}";

        $(document).ready( async function () {
            await getData();
        });

        async function getData() {

            $(".btn-submit").hide();

            const url = `${APP_URL}/api/inventory/${id}`;
            const method = "GET";
            const response = await request(url, method);

            if (response.status) {
                const params = Object.fromEntries(new FormData(document.getElementById("main-form")).entries());
                Object.keys(params).forEach(field => {
                    $(`#input-${field}`).attr("disabled", true);
                    $(`#input-${field}`).val(response.data[field]);
                })
            }
        }
    </script>
@endpush

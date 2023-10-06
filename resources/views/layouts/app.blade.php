@extends('layouts.default')
@section('baseStyles')
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/buttons.bootstrap4.min.css') }}">

    @stack('styles')
@endsection
@section('baseScripts')


    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/numbers.js') }}"></script>

    <script src="{{ asset('library/datatables/media/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/jszip.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/buttons.print.min.js') }}"></script>


    @stack('scripts')
@endsection
@section('body')
    <x-app.header/>
    <x-app.sidebar/>


    @yield('content')

    <x-app.footer/>
@endsection

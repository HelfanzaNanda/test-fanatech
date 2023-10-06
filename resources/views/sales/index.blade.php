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
                        @role(['SUPERADMIN', 'SALES'])
                            <button class="btn btn-primary btn-create">Create</button>
                        @endrole
                    </div>
                    <table id="main-table" class="table-hover table"></table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            drawDatatable()
        });

        function drawDatatable(){
            $("#main-table").DataTable({
                destroy: true,
                "pageLength": 10,
                "processing": true,
                "serverSide": true,
                "searching": true,
                // "responsive": true,
                "order": [[0, 'desc']],
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All'],
                ],
                "ajax": {
                    "url": `${APP_URL}/api/sales/datatables`,
                    "headers": { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        // d.filter = {
                        //     name : $('#input-filter-name').val(),
                        // }
                    }
                },
                "columns": [
                    {
                        searchable:false, orderable: false,
                        className: 'dt-control',
                        data: null,
                        defaultContent: '',
                        render : (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1 },
                    { title : 'Number', data: 'number', name: 'number', render : (data) => data || '' },
                    { title : 'Date', data: 'date', name: 'date', render : (data) => data || '' },
                    {
                        visible : ['SUPERADMIN', 'SALES'].includes(GLOBAL_ROLE_NAME), title : 'Action', data: null, name: 'action', orderable: false, width: '9%', render : (data, type, row, meta) => {
                            let action = ``
                            action += `<div class="dropdown d-inline mr-2">`;
                            action += `    <a href="#" class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">`;
                            action += `         <i class="fa-solid fa-ellipsis-vertical"></i>`
                            action += `    </a>`;
                            action += `    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 28px, 0px); top: 0px; left: 0px; will-change: transform;">`;
                            action += `        <a data-id="${data.id}" class="btn-edit dropdown-item" href="#">Edit</a>`;
                            // action += `        <a data-id="${data.id}" class="btn-detail dropdown-item" href="#">Detail</a>`;
                            action += `        <a data-id="${data.id}" class="btn-delete dropdown-item" href="#">Delete</a>`;
                            action += `    </div>`;
                            action += `</div>`;
                            return action;
                        }
                    }
                ],
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    },
                    title: "Report Sales",

                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    },
                    title: "Report Sales",

                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'A3',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    },
                    customize: function (doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    },
                    title: "Report Sales",
                }]
            })
        }

        $("#main-table").on('click', 'td.dt-control', function (e) {
            let tr = e.target.closest('tr');
            let row = $("#main-table").DataTable().row(tr);
            console.log('row : ', row);
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
            }
            else {
                // Open this row
                row.child(format(row.data())).show();
            }
        });

        function format(d) {
            console.log('d : ', d);
            let children = ``;
            children += `<table class="w-100">`;
            children += `    <thead>`;
            children += `        <tr>`;
            children += `            <th scope="col">#</th>`;
            children += `            <th scope="col">Inventory</th>`;
            children += `            <th scope="col">Qty</th>`;
            children += `            <th scope="col">Price</th>`;
            children += `        </tr>`;
            children += `    </thead>`;
            children += `    <tbody>`;
                                d.sales_details.forEach((item, index) => {
            children += `            <tr>`;
            children += `                <th scope="row">${index + 1}</th>`;
            children += `                <td>${item.inventory.name}</td>`;
            children += `                <td>${item.qty}</td>`;
            children += `                <td>${rupiahFormat(item.price)}</td>`;
            children += `            </tr>`;
                                })
            children += `    </tbody>`;
            children += `</table>`;
            return children;
        }

        $(document).on('click', '.btn-delete', async function (e) {
            e.preventDefault()
            let id = $(this).data('id')
            const isConfirmed = await showAlertDelete()
            if (isConfirmed) {
                const url = `${APP_URL}/api/sales/${id}`;
                const method = "DELETE";
                const response = await request(url, method);
                if (response.status) {
                    showSuccessAlert(response.message);
				    $('#main-table').DataTable().ajax.reload( null, false );
                }
            }
        })

        $(document).on('click', '.btn-create', async function (e) {
            e.preventDefault()
            const url = `${APP_URL}/sales/create`;
            location.href = url;
        })

        $(document).on('click', '.btn-edit', async function (e) {
            e.preventDefault()
            let id = $(this).data('id')

            const url = `${APP_URL}/sales/${id}/edit`;
            location.href = url;
        })
        $(document).on('click', '.btn-detail', async function (e) {
            e.preventDefault()
            let id = $(this).data('id')

            const url = `${APP_URL}/sales/${id}`;
            location.href = url;
        })
    </script>
@endpush

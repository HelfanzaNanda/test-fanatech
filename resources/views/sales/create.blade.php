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
                    @include('sales.form');
                    <div class="card-footer text-right">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary mr-2">Back</a>
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


        function selectRefresh() {
            $('.select2').select2({
                placeholder: "Select an Option",
                allowClear: true,
                ajax : {
                    url:`${APP_URL}/api/inventory/options`,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    headers: {
                         'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
        }


        $(document).ready( async function () {
            await generateNumber()
            addItem()
        });


        async function generateNumber() {
            const url = `${APP_URL}/api/generate/number`;
            const method = "POST";
            const params = {
                prefix : "SALES"
            }
            const response = await request(url, method, params);

            if (response.status) {
                $("#input-number").val(response.data.number);
            }
        }


        $(document).on('click', '.btn-add-item', async function (e) {
            e.preventDefault()
            addItem();
        })
        $(document).on('click', '.btn-remove-item', async function (e) {
            e.preventDefault()
            let id = $(this).data('id');
            const isConfirmed = await showAlertDelete()
            if (isConfirmed) {
                $(`#tr-${id}`).remove();
                calculateSubTotal();
            }
        })

        function addItem() {
            let dataId = $("#items-table tbody tr:last-child").data("id");
            let id = 0;
            if (Number.isInteger(dataId)) {
                id = dataId + 1;
            }
            console.log('id : ', id);
            let tr = ``;
                tr += `<tr data-id="${id}" id="tr-${id}">`;
                tr += `    <td>`;
                tr += `        <div class="form-group">`;
                tr += `            <select data-id="${id}" name="inventory_id[]" id="input-inventory_id-${id}" class="form-control select2 input-inventory_id"></select>`;
                tr += `        </div>`;
                tr += `    </td>`;
                tr += `    <td>`;
                tr += `         <input data-id="${id}" class="form-control input-stock" disabled type="text" name="stock[]" id="input-stock-${id}"> `;
                tr += `    </td>`;
                tr += `    <td>`;
                tr += `         <input disabled data-id="${id}" onkeypress="numberOnly(event)" class="form-control input-qty" type="text" name="qty[]" id="input-qty-${id}"> `;
                tr += `    </td>`;
                tr += `    <td>`;
                tr += `         <input disabled data-id="${id}" onkeypress="numberOnly(event)" class="form-control input-price" type="text" name="price[]" id="input-price-${id}"> `;
                tr += `    </td>`;
                tr += `    <td>`;
                tr += `         <input data-id="${id}" class="form-control input-total" disabled type="text" name="total[]" id="input-total-${id}"> `;
                tr += `    </td>`;
                if (id > 0) {
                    tr += `    <td>`;
                    tr += `         <a href="#" data-id="${id}" class="btn btn-remove-item btn-icon btn-danger"><i class="fas fa-times"></i></a>`;
                    tr += `    </td>`;
                }
                tr += `</tr>`;

            $("#items-table").append(tr);
            selectRefresh()
        }

        $(document).on("change", ".input-inventory_id", function () {
            const id = $(this).data('id');
            const data = $(this).select2('data')[0];
            console.log("DATA : ", data);
            $(`#input-stock-${id}`).val(data.stock)
            $(`#input-price-${id}`).val(rupiahFormat(data.price))
            $(`#input-price-${id}`).attr("data-price", data.price)
            $(`#input-qty-${id}`).attr("disabled", false)
            // $(`#input-price-${id}`).attr("disabled", false)
        });

        // $(document).on("input", ".input-price", function (e) {
        //     const id = $(this).data('id');
        //     calculateTotal(id);
        // });
        $(document).on("input", ".input-qty", function (e) {
            const id = $(this).data('id');
            const oldQty = $(this).data('qty');

            // const stock = $(`#input-stock-${id}`).val();
            const data = $(`#input-inventory_id-${id}`).select2('data')[0];
            const stock = data.stock;

            const qty = $(this).val();
            const remaining_stock = stock - qty;
            if (remaining_stock < 1) {
                e.preventDefault();
                $(this).val(oldQty);

                showFailedAlert("qty has reached the maximum limit");
                return false;
            }

            // $(`#input-stock-${id}`).val(remaining_stock);

            $(this).data('qty', qty);
            calculateTotal(id);
        });

        function calculateTotal(key) {


            let qty = $(`#input-qty-${key}`).val();
            // let price = $(`#input-price-${key}`).val();
            let price = $(`#input-price-${key}`).data("price");


            qty = parseInt(qty)
            price = parseFloat(price)

            if (isNaN(qty)) {
                qty = 0;
            }
            if (isNaN(price)) {
                price = 0;
            }
            let total = qty * price;
            $(`#input-total-${key}`).data("price", total)
            $(`#input-total-${key}`).val(rupiahFormat(total))

            calculateSubTotal();
        }


        function calculateSubTotal() {
            let totalQty = 0;
            let total = 0;
            $(".input-qty").each(function (item) {
                totalQty += $(this).val() ? parseInt($(this).val()) : 0;
            })
            $(".input-total").each(function (item) {
                total += $(this).data("price") ? parseFloat($(this).data("price")) : 0;
            })

            if (isNaN(totalQty)) {
                totalQty = 0;
            }
            console.log('total before : ', total);

            if (isNaN(total)) {
                total = 0;
            }
            console.log('total after : ', total);

            $(".txt-total-qty").html(totalQty)
            $(".txt-subtotal").html(rupiahFormat(total))
        }


        $(document).on('submit', '#main-form', async function (e) {
            e.preventDefault()
            const url = `${APP_URL}/api/sales`;
            const method = "POST";

            const inputInventories = $('[id^=input-inventory_id]');
            console.log('inputInventories : ', inputInventories);

            // const qty = $('[id^=input-qty]').val();
            // const inputPrices = $('[id^=input-price]');
            const items = [];
            $(inputInventories).each(function( index ) {
                const key = $(this).attr("id").match(/\d+/);
                const inventory_id = $(this).val();
                const qty = $(`#input-qty-${key}`).val();
                const price = $(`#input-price-${key}`).val();
                const item = { inventory_id, qty, price, };
                items.push(item);
            });

            // console.log('inventory_id : ', inventory_id);
            console.log('items : ', items);
            const params = {
                items : items
            };


            const response = await request(url, method, params);
            if (response.status) {
                showAlertOnSubmit(response, '', '', `${APP_URL}/sales`);
            }
        })
    </script>
@endpush

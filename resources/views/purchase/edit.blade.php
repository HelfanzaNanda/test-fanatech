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
                    @include('purchase.form');
                    <div class="card-footer text-right">
                        <a href="{{ route('purchase.index') }}" class="btn btn-secondary mr-2">Back</a>
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
        const id = "{{ $id }}";


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
            await getData();
            // addItem()
        });

        async function getData() {
            const url = `${APP_URL}/api/purchase/${id}`;
            const method = "GET";
            const response = await request(url, method);

            if (response.status) {

                const data = response.data;
                $("#input-date").val(data.date);
                $("#input-number").val(data.number);

                data.purchase_details.forEach((item, index) => {
                    addItem(item)
                    calculateTotal(index)
                })
                calculateSubTotal()
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

        function addItem(item = null) {
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
                tr += `         <input value="${item ? item.inventory.stock : ''}" data-id="${id}" class="form-control input-stock" disabled type="text" name="stock[]" id="input-stock-${id}"> `;
                tr += `    </td>`;
                tr += `    <td>`;
                tr += `         <input value="${item ? item.qty : ''}" data-id="${id}" onkeypress="numberOnly(event)" class="form-control input-qty" type="text" name="qty[]" id="input-qty-${id}"> `;
                tr += `    </td>`;
                tr += `    <td>`;
                tr += `         <input disabled data-price="${item ? item.price : ''}" value="${item ? rupiahFormat(item.price) : ''}" data-id="${id}" onkeypress="numberOnly(event)" class="form-control input-price" type="text" name="price[]" id="input-price-${id}"> `;
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

            if (item) {
                var option = `<option data-stock="${item.inventory.stock}" value="${item.inventory.id}" selected>${item.inventory.name}</option>`;
                $(`#input-inventory_id-${id}`).append(option).trigger('change');
                $(`#input-inventory_id-${id}`).val(item.inventory.id).trigger('change');
            }
        }

        $(document).on("change", ".input-inventory_id", function () {
            const id = $(this).data('id');
            const data = $(this).select2('data')[0];
            let stock = data.stock;
            let price = data.price;

            if (!stock) {
                stock = $(this).find(":selected").data("stock")
            }else{
                $(`#input-qty-${id}`).attr("disabled", false)
            }
            if (!price) {
                price = $(`#input-price-${id}`).data("price")
            }else{
                $(`#input-price-${id}`).attr("data-price", price)
                $(`#input-price-${id}`).val(rupiahFormat(price))
            }
            console.log("DATA : ", data);
            $(`#input-stock-${id}`).val(stock)
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
            let stock = data.stock;
            if (!stock) {
                stock = $(`#input-inventory_id-${id}`).find(":selected").data("stock")
            }


            const qty = $(this).val();
            let remaining_stock = parseInt(stock) + parseInt(qty);
            if (remaining_stock < 1) {
                e.preventDefault();
                $(this).val(oldQty);

                showFailedAlert("qty has reached the maximum limit");
                return false;
            }

            if (isNaN(remaining_stock)) {
                remaining_stock = oldQty;
            }

            // $(`#input-stock-${id}`).val(remaining_stock);

            $(this).data('qty', qty);
            calculateTotal(id);
        });

        function calculateTotal(key) {


            let qty = $(`#input-qty-${key}`).val();
            // let price = $(`#input-price-${key}`).val();
            let price = $(`#input-price-${key}`).data("price")



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
            const url = `${APP_URL}/api/purchase/${id}`;
            const method = "PUT";

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
                showAlertOnSubmit(response, '', '', `${APP_URL}/purchase`);
            }
        })
    </script>
@endpush

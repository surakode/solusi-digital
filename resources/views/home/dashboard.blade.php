@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Data {{ $title ?? '' }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Data {{ $title ?? '' }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        {{-- ./Content Header --}}

        <section class="content">

            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar {{ $title ?? '' }} </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm"
                                    onclick="formHandler('{{ route('dashboard.create') }}')" title="Collapse">
                                    <i class="fas fa-plus"></i> Tambah Barang
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- /.Filter Box -->
                            <table id="dTable" class="table table-bordered table-striped table-hover" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr align="center">
                                        <th> No </th>
                                        <th> Nama Barang </th>
                                        <th> Harga </th>
                                        <th> Stock </th>
                                        <th> Gambar </th>
                                        <th> Kode </th>
                                        <th> Action </th>
                                </thead>
                                <tbody class="tBody">
                                    @foreach ($items as $key => $item)
                                        <tr class="text-center">
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>Rp. {{ \Helper::setCurrency($item->price) }}</td>
                                            <td>{{ $item->stock }}</td>
                                            <td><img src="/img/{{ $item->picture }}" class="img-thumbnail"
                                                    style="max-height: 80px"></td>
                                            <td>{{ $item->code }}</td>
                                            <td>
                                                <a href="javascript:void(0);" id="_bCart" data-toggle="tooltip"
                                                    data-original-title="Add cart" data-id="{{ $item->id }}"
                                                    class="disable btn btn-outline-success btn-xs disabling"
                                                    data-toggle="tooltip" data-placement="bottom" title="Non-Aktifkan"> <i
                                                        class="fas fa-cart-plus"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                        </div>

                        <div class="card-footer">
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Keranjang </h3>
                        </div>
                        <div class="card-body">
                            <table id="dTable" class="table table-bordered table-striped table-hover" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr align="center">
                                        <th> Nama Barang </th>
                                        <th> Harga </th>
                                        <th> Qty </th>
                                        <th> Subtotal </th>
                                        <th> Action </th>
                                </thead>
                                <tbody class="tBody">
                                    @foreach ($carts as $cart)
                                        <tr>
                                            <td>{{ $cart->name }}</td>
                                            <td>Rp. {{ \Helper::setCurrency($cart->price) }}</td>
                                            <td>{{ $cart->qty }}</td>
                                            <td>Rp. {{ \Helper::setCurrency($cart->subtotal) }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.plusCart', $cart->id) }}" id="_bPlus"
                                                    data-toggle="tooltip" data-original-title="Tambah"
                                                    data-id="{{ $cart->id }}"
                                                    class="disable btn btn-outline-success btn-xs disabling"
                                                    data-toggle="tooltip" data-placement="bottom" title="Non-Aktifkan"> <i
                                                        class="fas fa-plus"></i>
                                                </a>

                                                <a href="{{ route('dashboard.minusCart', $cart->id) }}" id="_bMinus"
                                                    data-toggle="tooltip" data-original-title="Kurang"
                                                    data-id="{{ $cart->id }}"
                                                    class="disable btn btn-outline-warning btn-xs disabling"
                                                    data-toggle="tooltip" data-placement="bottom" title="Non-Aktifkan"> <i
                                                        class="fas fa-minus"></i>
                                                </a>

                                                <a href="javascript:void(0);" id="_bDelete" data-toggle="tooltip"
                                                    data-original-title="Delete" data-id="{{ $cart->id }}"
                                                    class="disable btn btn-outline-danger btn-xs disabling"
                                                    data-toggle="tooltip" data-placement="bottom" title="Non-Aktifkan"> <i
                                                        class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><b>Total</b></td>
                                        <td colspan="3" class="text-right"><b> Rp.
                                                {{ \Helper::setCurrency($sumCart) }}</b></td>
                                        <td>
                                            @if ($sumCart > 0)
                                                <button type="button" class="btn btn-success btn-sm"
                                                    href="javascript:void(0);" id="_bCheckout" data-toggle="tooltip"
                                                    data-original-title="Delete" data-id="{{ $cart->id }}"
                                                    title="Collapse"> Checkout
                                                </button>
                                            @endif

                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            {{-- modal Edit Data --}}
            <div class="modal fade" id="modalBlade" tabindex="-1" role="dialog" aria-labelledby="modalBladeLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body" id="modalBody">
                            {{-- getByAJAX --}}
                        </div>
                    </div>
                </div>
                {{-- ./modal Edit Data --}}
        </section>
    </div>
@endsection

@section('jScript')
    <script>
        function formHandler(href) {
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    if (result.status === "error") {
                        Swal.fire(
                            result.status == 'success' ? 'Berhasil !' : 'Gagal !',
                            result.message,
                            result.status
                        )
                    } else {
                        $('#modalBlade').modal("show");
                        $('#modalBody').html(result).show();

                    }
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    // console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        }

        // handling disable user
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('body').on('click', '#_bCart', function() {
                var _id = $(this).data("id");

                Swal.fire({
                    title: 'Tambah Ke Keranjang',
                    text: "Tekan Iya jika setuju !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38BC8C',
                    cancelButtonColor: '#E74C3C',
                    confirmButtonText: 'Yes .. !',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "post",
                            url: "/dashboard/addCart",
                            data: {
                                "id": _id
                            },
                            success: function(data) {
                                // dTable.draw(false);

                                Swal.fire(
                                    data.status == 'success' ? 'Success !' :
                                    'Failed !',
                                    data.message,
                                    data.status
                                )

                                if (data.status == 'success') {
                                    setTimeout(window.location.reload(true), 1000);
                                }

                            },
                            error: function(err) {
                                Swal.fire(
                                    err.status + ' !',
                                    err.message,
                                    err.status
                                )
                            }
                        });
                    }
                });
            });

            $('body').on('click', '#_bDelete', function() {
                var _id = $(this).data("id");

                Swal.fire({
                    title: 'Hapus Dari Keranjang',
                    text: "Tekan Iya jika setuju !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38BC8C',
                    cancelButtonColor: '#E74C3C',
                    confirmButtonText: 'Yes .. !',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "post",
                            url: "/dashboard/deleteCart",
                            data: {
                                "id": _id
                            },
                            success: function(data) {
                                // dTable.draw(false);

                                Swal.fire(
                                    data.status == 'success' ? 'Success !' :
                                    'Failed !',
                                    data.message,
                                    data.status
                                )

                                if (data.status == 'success') {
                                    setTimeout(window.location.reload(true), 1000);
                                }

                            },
                            error: function(err) {
                                Swal.fire(
                                    err.status + ' !',
                                    err.message,
                                    err.status
                                )
                            }
                        });
                    }
                });
            });

            $('body').on('click', '#_bCheckout', function() {
                var _id = $(this).data("id");

                Swal.fire({
                    title: 'Checkout ??',
                    text: "Pastikan baranng belanjaan benar !!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#38BC8C',
                    cancelButtonColor: '#E74C3C',
                    confirmButtonText: 'Yes .. !',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "post",
                            url: "/dashboard/deleteCart",
                            data: {
                                "id": _id
                            },
                            success: function(data) {
                                // dTable.draw(false);

                                Swal.fire(
                                    data.status == 'success' ? 'Success !' :
                                    'Failed !',
                                    data.message,
                                    data.status
                                )

                                if (data.status == 'success') {
                                    setTimeout(window.location.reload(true), 1000);
                                }

                            },
                            error: function(err) {
                                Swal.fire(
                                    err.status + ' !',
                                    err.message,
                                    err.status
                                )
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

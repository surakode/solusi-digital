<form id="formSubmit">
    <center>
        <h2 class="pt-3 text-info">{{ $title }}</h2>
    </center>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" class="form-control" name="name" required>
            </div>

            <div class="form-group">
                <label>Harga</label>
                <input type="number" class="form-control" name="price" required>
            </div>

            <div class="form-group">
                <label>Stock</label>
                <input type="number" class="form-control" name="stock" required>
            </div>

            <div class="form-group">
                <label>Foto</label>
                <div class="input-group">
                    <input type="file" accept="image/*" class="form-control" name="picture" required>
                </div>
            </div>
        </div>


        <div class="col-6">

        </div>
        <div class="col-6">
            <div class="row">
                <div class="col-6">
                    <button type="button" data-dismiss="modal" class="btn btn-block btn-info btn-sm">Back</button>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btn-block btn-info btn-sm">Save</button>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    $(document).ready(function() {
        $('#formSubmit').submit(function(e) {
            e.preventDefault();

            // alert('test');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('dashboard.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                // return the result
                beforeSend: function() {
                    $('#loader').show();
                },
                success: function(result) {
                    // dTable.draw(false);
                    if (result.status == 'success') {
                        $('#modalBlade').modal("hide");
                        Swal.fire(
                            result.status == 'success' ? 'Success !' : 'Failed !',
                            result.message,
                            result.status

                        )

                        if (result.status == 'success') {
                            setTimeout(window.location.reload(true), 1000);
                        }
                    } else {
                        Swal.fire(
                            result.status == 'success' ? 'Success !' : 'Failed !',
                            result.message,
                            result.status
                        )
                    }
                },
                complete: function(result) {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    Swal.fire(
                        error.status + ' !',
                        error.message,
                        error.status
                    )
                    $('#loader').hide();
                }
            })
        });
    })
</script>

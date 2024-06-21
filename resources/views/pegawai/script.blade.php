<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script>
    $(document).ready(function(){
        $('#myTable').DataTable({
            processing: true,
            serverside: true,
            ajax: "{{ url('pegawaiAjax') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama',
                    name: 'Nama'
                },
                {
                    data: 'email',
                    name: 'Email'
                },
                {
                    data: 'aksi',
                    name: 'Aksi'
                }
            ]
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function resetTable(){
            $('#myTable').DataTable().ajax.reload();
        }

        function resetModal(){
            $('#nama').val('');
            $('#email').val('');
        }

        function resetAlert(){
            $('.alert-danger').addClass('d-none').html('');
            $('.alert-success').addClass('d-none').html('');
        }

        function simpan(id = ''){
            let nama = $('#nama').val();
            let email = $('#email').val();

            let url, type;
            if (!id) {
                url = 'pegawaiAjax';
                type = 'POST';
            } else {
                url = 'pegawaiAjax/' + id;
                type = 'PUT';
            }

            $.ajax({
                url: url,
                type: type,
                data: {
                    nama: nama,
                    email: email
                },
                success: function(response){
                    resetAlert();
                    if (response.errors) {
                        $('.alert-danger').removeClass('d-none');
                        $('.alert-danger').append("<ul>");
                        $.each(response.errors, function(key, value){
                            $('.alert-danger').find('ul').append(`<li> ${value} </li>`);
                        });
                        $('.alert-danger').append("</ul>");
                    } else {
                        $('.alert-success').removeClass('d-none').html(response.success);
                    }
                    resetTable();
                }
            });
        }

        $(document).on('click', '.tambah', function(e){
            e.preventDefault();
            $('#staticBackdrop').modal('show');
            resetAlert();
            resetModal();
            $('.update').addClass('d-none');
            $('.simpan').removeClass('d-none');
        });

        $(document).on('click', '.simpan', function(){
            simpan();
        });

        $(document).on('click', '.edit', function(){
            let id = $(this).data('id');
            $.ajax({
                url: `pegawaiAjax/${id}/edit`,
                type: 'GET',
                success: function(response){
                    let nama = response.result.nama;
                    let email = response.result.email;

                    $('#staticBackdrop').modal('show');
                    resetAlert();

                    $('#nama').val(nama);
                    $('#email').val(email);
                    $('.update').data('id', id).removeClass('d-none');
                    $('.simpan').addClass('d-none');
                }
            });
        });

        $(document).on('click', '.update', function(){
            let id = $(this).data('id');
            simpan(id);
        });

        $(document).on('click', '.hapus', function(){
            if(confirm('Anda yakin ingin menghapus?')){
                let id = $(this).data('id');
                $.ajax({
                    url: 'pegawaiAjax/' + id,
                    type: 'DELETE',
                    success: function(response){
                        if(response.success){
                            resetTable();
                        }else{
                            alert(response.errors);
                        }
                    }
                });
            }
        });
    });
</script>
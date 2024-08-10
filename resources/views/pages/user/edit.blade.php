@extends('layouts.app')

@push('after-style')
@endpush

@section('title')
  Edit Data Pengguna
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('home') }}">Dashboard</a>
        </li>

        <li class="breadcrumb-item">
          <a href="{{ route('user.index') }}">Daftar Pengguna</a>
        </li>

        <li class="breadcrumb-item active">Edit Daftar Pengguna</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Daftar Pengguna</h5>

            <a href="{{ route('user.index') }}">
              <button type="button" class="btn btn-secondary btn-icon-text">
                <i class="fas fa-arrow-left btn-icon-prepend"></i>
                Kembali
              </button>
            </a>
          </div>

          <div class="card-body">
            <form action="{{ route('user.update', $get_data->id) }}" method="POST" id="form">
              @csrf
              @method('PUT')

              <div class="mb-3 row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="name">Nama Lengkap<span style="color: red">*</span></label>
                  <input type="text" class="form-control" id="name" name="name"
                    placeholder="Masukkan nama pengguna" value="{{ $get_data->name ?? old('name') }}" />
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="email">Email Aktif<span style="color: red">*</span></label>
                  <input type="email" class="form-control" id="email" name="email"
                    placeholder="Masukkan email pengguna" value="{{ $get_data->email ?? old('email') }}" />
                </div>
              </div>
              
              <div class="mb-3 row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="nip">Nomor Induk Pegawai<span style="color: red">*</span></label>
                  <input type="text" class="form-control" id="nip" name="nip"
                    placeholder="Masukkan nip pengguna" value="{{ $get_data->nip ?? old('nip') }}" />
                </div>

                <div class="mb-3 col-md-6">
                  <label class="form-label" for="roles">Hak Akses</label>

                  <select class="form-control select2" name="role_id" id="role_id" style="width: 100%">
                    <option value="">Pilih Hak Akses</option>
                    @foreach ($roles as $item)
                      <option value="{{ $item->id }}" {{ IsSelected($item->id, $get_data->role_id) }}>
                        {{ $item->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <button type="submit" class="btn btn-primary btn-simpan">Simpan</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('after-script')
  <script>
    $(function() {
      $('.select2').select2({
        theme: "bootstrap-5"
      });

      $('.btn-simpan').on('click', function() {
        $('#form').ajaxForm({
          success: function(response) {
            if (response.status == true) {
              swal({
                  title: "Success!",
                  text: "Berhasil Menyimpan Data",
                  icon: "success"
                })
                .then(function() {
                  document.location = "{{ route('user.index') }}";
                });
            } else {
              var pesan = "";
              var data_pesan = response.pesan;
              const wrapper = document.createElement('div');

              if (typeof(data_pesan) == 'object') {
                jQuery.each(data_pesan, function(key, value) {
                  console.log(value);
                  pesan += value + '. <br>';
                  wrapper.innerHTML = pesan;
                });

                swal({
                  title: "Error!",
                  content: wrapper,
                  icon: "warning"
                });
              } else {
                swal({
                  title: "Error!",
                  text: response.pesan,
                  icon: "warning"
                });
              }
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            var err = eval("(" + jqXHR.responseText + ")");
            swal("Error!", err.Message, "error");
          }
        })
      })
    })
  </script>
@endpush

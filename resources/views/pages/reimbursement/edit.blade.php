@extends('layouts.app')

@push('after-style')
@endpush

@section('title')
  Ubah Pengajuan
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('/home') }}">Dashboard</a>
        </li>

        <li class="breadcrumb-item">
          <a href="{{ route('reimbursement.index') }}">Data Pengajuan Reimbursement</a>
        </li>

        <li class="breadcrumb-item active">Ubah Pengajuan</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ubah Pengajuan</h5>

            <a href="{{ route('reimbursement.index') }}">
              <button type="button" class="btn btn-secondary btn-icon-text">
                <i class="fas fa-arrow-left btn-icon-prepend"></i>
                Kembali
              </button>
            </a>
          </div>

          <div class="card-body">

            <form action="{{ route('reimbursement.update', $item->id) }}" method="POST" id="form-customer-item-data"
              class="mb-3">
              @csrf
              @method('PUT')

              <div class="mb-3 row">
                <div class="mb-3 col-6">
                  <label class="form-label" for="name">Nama Pengajuan<span style="color: red">*</span></label>

                  <input type="text" class="form-control" id="name" name="name"
                    placeholder="Masukkan nama pengajuan" value="{{ $item->name ?? old('name') }}" />
                </div>

                <div class="mb-3 col-6">
                  <label class="form-label" for="date_created">Tanggal Pengajuan<span style="color: red">*</span></label>

                  <input type="date" class="form-control" id="date_created" name="date_created"
                    value="{{ $item->date_created ? \Carbon\Carbon::createFromFormat('Y-m-d', $item->date_created)->format('Y-m-d') : old('date_created') }}" />
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="description">Deskripsi Pengajuan<span style="color: red">*</span></label>

                <textarea class="form-control" id="description" name="description" placeholder="Masukkan deskripsi detail pengajuan"
                  cols="30" rows="5">{{ $item->description ?? old('description') }}</textarea>
              </div>
              
              <div class="mb-3">
                @if ($item->file != null)
                  @if (Storage::disk('public')->exists($item->file))
                    <div class="mb-2">
                      <a href="{{ Storage::url($item->file) }}" class="btn btn-sm btn-dark" download>
                        Download File Pendukung
                      </a>
                    </div>
                  @endif
                @endif

                <label for="file" class="form-label">File Pendukung</label>
                <input class="form-control" type="file" id="file" name="file">

                <h5 class="mt-2">Catatan:</h5>

                <ul>
                  <li>File yang diunggah harus dalam format image/pdf</li>
                  <li>Ukuran maksimum file yang diizinkan adalah 5MB.</li>
                </ul>
              </div>

              <a href="javascript:void(0)" class="btn btn-primary btn-simpan">Simpan</a>
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
      let CSRF_TOKEN = "{{ csrf_token() }}";

      $(document).on("click", ".btn-simpan", function(event) {
        $('.btn-simpan').text('Menyimpan...'); //change button text
        $('.btn-simpan').attr('disabled', true); //set button disable

        var url = $('#form-customer-item-data').attr('action');
        var type = $('#form-customer-item-data').attr('method');

        var form = $('#form-customer-item-data')[0];
        var formData = new FormData(form);
        event.preventDefault();

        // ajax adding data to database
        $.ajax({
          url: url,
          type: type,
          cache: false,
          contentType: false,
          processData: false,
          data: formData,
          dataType: "JSON",
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
          },
          success: function(response) {
            $('.btn-simpan').text('Simpan'); //change button text
            $('.btn-simpan').attr('disabled', false); //set button enable

            if (response.status == true) //if success close modal and reload ajax table
            {
              var span = document.createElement("span");
              span.innerHTML = "" + response.pesan + "";

              swal({
                  html: true,
                  title: "Success!",
                  content: span,
                  icon: "success"
                })
                .then(function() {
                  document.location = "{{ route('reimbursement.index') }}";
                });
            } else {
              var pesan = "";
              var data_pesan = response.pesan;
              const wrapper = document.createElement('div');
              if (typeof(data_pesan) == 'object') {
                jQuery.each(data_pesan, function(key, value) {
                  console.log(value);
                  pesan += value + '<br>';
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
            $('.btn-simpan').text('Simpan'); //change button text
            $('.btn-simpan').attr('disabled', false); //set button enable

            var err = eval("(" + jqXHR.responseText + ")");

            swal("Error!", err.Message, "error");
          }
        });
      });
    })
  </script>
@endpush

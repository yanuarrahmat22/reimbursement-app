@extends('layouts.app')

@push('after-style')
  <style>
    div.tagsinput span.tag {
      background: #2980b9;
      color: #ecf0f1;
      padding: 4px;
      margin: 1px;
      font-size: 14px;
      text-transform: lowercase !important;
      border: none;
    }

    div.tagsinput span.tag a {
      color: #ecf0f1;
    }
  </style>
@endpush

@section('title')
  Tambah Data Menu
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('home') }}">Dashboard</a>
        </li>

        <li class="breadcrumb-item">
          <a href="{{ route('menu.index') }}">Data Menu</a>
        </li>

        <li class="breadcrumb-item active">Tambah Data Menu</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah Data Menu</h5>

            <a href="{{ route('menu.index') }}">
              <button type="button" class="btn btn-secondary btn-icon-text">
                <i class="fas fa-arrow-left btn-icon-prepend"></i>
                Kembali
              </button>
            </a>
          </div>

          <div class="card-body">
            <form action="{{ route('menu.store') }}" method="POST" id="form">
              @csrf

              <div class="mb-3">
                <label class="form-label" for="upid">Parent Menu</label>

                <select name="upid" id="upid" class="form-control select2" style="width: 100%">
                  <option value="">Pilih Parent Menu</option>
                  @foreach ($menus as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label" for="name">Nama Menu<span style="color: red">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama menu"
                  value="{{ old('name') }}" />
              </div>

              <div class="mb-3">
                <label class="form-label" for="code">Kode Menu<span style="color: red">*</span></label>
                <input type="text" class="form-control" id="code" name="code" placeholder="Masukkan kode menu"
                  value="{{ old('code') }}" />
              </div>

              <div class="mb-3">
                <label class="form-label" for="position">Posisi Menu<span style="color: red">*</span></label>
                <input type="number" class="form-control" id="position" name="position"
                  placeholder="Masukkan posisi menu" value="{{ old('position') }}" />

                <div class="form-text">
                  Contoh: 1
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="link">Link Menu<span style="color: red">*</span></label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text" id="link">https://example.com/</span>
                  <input type="text" class="form-control" id="link" name="link"
                    aria-describedby="basic-addon34" value="{{ old('link') }}">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="icon">Icon Menu<span style="color: red">*</span></label>
                <input type="text" class="form-control" id="icon" name="icon" placeholder="Masukkan icon menu"
                  value="{{ old('icon') }}" />

                <div class="form-text">
                  Contoh: fas fa-home
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="permission">Permission Menu<span style="color: red">*</span></label>
                <input type="text" class="form-control input-tags" id="permission" name="permission"
                  placeholder="Masukkan aksi menu" value="{{ old('permission') }}" />
              </div>

              <div class="mb-3">
                <label class="form-label" for="description">Deskripsi Menu</label>

                <textarea class="form-control" name="description" id="description" cols="20" rows="5"
                  placeholder="Masukkan deskripsi menu">{{ old('description') }}</textarea>
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
      if ($('.select2').length) {
        $('.select2').select2({
          theme: "bootstrap-5"
        });
      }

      $('.input-tags').tagsInput({
        'width': '100%',
        'height': '75%',
        'interactive': true,
        'defaultText': 'gunakan koma',
        'removeWithBackspace': true,
        'placeholderColor': '#666666'
      });

      $('.btn-simpan').on('click', function() {
        $('#form').ajaxForm({
          success: function(response) {
            if (response.status == true) {
              swal({
                  title: "Success!",
                  text: response.pesan,
                  icon: "success"
                })
                .then(function() {
                  document.location = "{{ route('menu.index') }}";
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

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
  Data Authorization Hak Akses
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('home') }}">Dashboard</a>
        </li>

        <li class="breadcrumb-item">
          <a href="{{ route('role.index') }}">Data Hak Akses</a>
        </li>

        <li class="breadcrumb-item active">Configure Data Authorization Hak Akses</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Configure Authorization Hak Akses</h5>

            <a href="{{ route('role.index') }}">
              <button type="button" class="btn btn-secondary btn-icon-text">
                <i class="fas fa-arrow-left btn-icon-prepend"></i>
                Kembali
              </button>
            </a>
          </div>

          <div class="card-body">
            <form action="{{ route('role.usermenuauthorization.store') }}" method="POST" id="form">
              @csrf

              <input type="hidden" name="role_id" value="{{ $data->id }}">

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="name">Nama Role Akses</label>
                <div class="col-sm-10">
                  <label class="col-form-label">: {{ $data->name }}</label>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="code">Kode Role Akses</label>
                <div class="col-sm-10">
                  <label class="col-form-label">: {{ $data->code }}</label>
                </div>
              </div>

              <div class="table-responsive text-nowrap">
                <table class="table" style="width: 100%">
                  <thead>
                    <tr>
                      <th></th>
                      <th width="20%">Name Menu</th>
                      <th>Aksi</th>
                      <th>Publish</th>
                    </tr>
                  </thead>

                  <tbody class="table-border-bottom-0">
                    @foreach ($menus as $mdl)
                      @php
                        $daction_now = $user_menu_authorizations
                            ->where('role_id', $data->id)
                            ->where('menu_id', $mdl->id)
                            ->first();
                        
                        $action_now = $daction_now ? $daction_now->permission_given : '';
                        $publish = $daction_now ? $daction_now->status : '';
                      @endphp
                      <tr>
                        <td></td>
                        <td style="vertical-align: top"><strong>{{ $mdl->name }}</strong></td>
                        <td>
                          <input type="text" class="form-control laravel-tags" name="menu[{{ $mdl->id }}]"
                            value="{{ $action_now }}">
                          <br> action : {{ $mdl->permission }}
                        </td>
                        <td style="vertical-align: top">
                          <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="publish[{{ $mdl->id }}]"
                              value="1" {{ $publish == 1 ? 'checked' : '' }}>
                          </div>
                        </td>
                      </tr>

                      @foreach ($mdl->menus as $smdl)
                        @php
                          $daction_now = $user_menu_authorizations
                              ->where('role_id', $data->id)
                              ->where('menu_id', $smdl->id)
                              ->first();
                          
                          $action_now = $daction_now ? $daction_now->permission_given : '';
                          $publish = $daction_now ? $daction_now->status : '';
                        @endphp
                        <tr>
                          <td></td>
                          <td style="vertical-align: top">&emsp;&emsp;{{ $smdl->name }}</td>
                          <td>
                            <input type="text" class="form-control laravel-tags" name="menu[{{ $smdl->id }}]"
                              value="{{ $action_now }}">
                            <br> action : {{ $smdl->permission }}
                          </td>
                          <td style="vertical-align: top">
                            <div class="form-check form-switch mb-2">
                              <input class="form-check-input" type="checkbox" name="publish[{{ $smdl->id }}]"
                                value="1" {{ $publish == 1 ? 'checked' : '' }}>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    @endforeach
                  </tbody>
                </table>
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
    $('.laravel-tags').tagsInput({
      'width': '100%',
      'height': '40px',
      'interactive': true,
      'defaultText': 'gunakan koma',
      'removeWithBackspace': true,
      'placeholderColor': '#666666'
    });

    $(function() {

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
                  document.location = "{{ route('role.index') }}";
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
      });
    })
  </script>
@endpush

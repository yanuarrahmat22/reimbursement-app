@extends('layouts.app')

@push('after-style')
@endpush

@section('title')
  Data Menu
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('home') }}">Dashboard</a>
        </li>

        <li class="breadcrumb-item active">Daftar Menu</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Menu</h5>

            @if (isAccess('create', $get_menu, auth()->user()->role_id))
              <a href="{{ route('menu.create') }}">
                <button type="button" class="btn btn-primary btn-icon-text">
                  <i class="fa fa-plus btn-icon-prepend"></i>
                  Tambah
                </button>
              </a>
            @endif
          </div>

          <div class="card-body">
            <div class="accordion" id="menuAccordion">

              @forelse ($data as $menu)
                @php
                  $id_menu = get_menu_id('menu');

                  //selalu bisa
                  $detailButton =
                      '<a class="btn btn-sm btn-info" href="' . route('menu.show', $menu->id) . '">Detail</a>';

                  $editButton = '';
                  if (isAccess('update', $id_menu, auth()->user()->role_id)) {
                      $editButton =
                          '<a href="' . route('menu.edit', $menu->id) . '" class="btn btn-sm btn-warning">Edit</a>';
                  }

                  $deleteButton = '';
                  if (isAccess('delete', $id_menu, auth()->user()->role_id)) {
                      $deleteButton =
                          '<a class="btn-delete btn btn-sm btn-danger" href="javascript:void(0)" data-id="' .
                          $menu->id .
                          '" data-nama="' .
                          $menu->name .
                          '">Hapus</a>';
                  }

                  $action_induk = ' ' . $editButton . ' ' . $detailButton . ' ' . $deleteButton . '';
                @endphp

                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingOne_{{ $menu->id }}">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                      data-bs-target="#collapseOne_{{ $menu->id }}" aria-expanded="true"
                      aria-controls="collapseOne_{{ $menu->id }}">
                      {{ $menu->name }}
                    </button>
                  </h2>

                  <div id="collapseOne_{{ $menu->id }}" class="accordion-collapse collapse"
                    aria-labelledby="headingOne_{{ $menu->id }}" data-bs-parent="#menuAccordion">
                    <div class="accordion-body">
                      <div class="d-flex justify-content-between">
                        <div>{{ $menu->name }}</div>
                        <div>
                          {!! $action_induk !!}
                        </div>
                      </div>

                      {{-- jika ternyata ada submenu --}}
                      @if (count($menu->menus) > 0)
                        <div class="mt-3">
                          <h6>Submenu</h6>
                          <ul class="list-group">

                            @foreach ($menu->menus as $submenu)
                              @php
                                $id = get_menu_id('menu');

                                //selalu bisa
                                $detailButtonChild =
                                    '<a class="btn btn-sm btn-info" href="' .
                                    route('menu.show', $submenu->id) .
                                    '">Detail</a>';

                                $editButtonChild = '';
                                if (isAccess('update', $id, auth()->user()->role_id)) {
                                    $editButtonChild =
                                        '<a href="' .
                                        route('menu.edit', $submenu->id) .
                                        '" class="btn btn-sm btn-warning">Edit</a>';
                                }

                                $deleteButtonChild = '';
                                if (isAccess('delete', $id, auth()->user()->role_id)) {
                                    $deleteButtonChild =
                                        '<a class="btn-delete btn btn-sm btn-danger" href="javascript:void(0)" data-id="' .
                                        $submenu->id .
                                        '" data-nama="' .
                                        $submenu->name .
                                        '">Hapus</a>';
                                }

                                $action_child =
                                    ' ' . $editButtonChild . ' ' . $detailButtonChild . ' ' . $deleteButtonChild . '';
                              @endphp

                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $submenu->name }}
                                <div>
                                  {!! $action_child !!}
                                </div>
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @empty
                <p>Belum Ada Menu</p>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('after-script')
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script>
    $(document).ready(function() {
      $('#menuAccordion').on('click', '.btn-delete', function() {
        var kode = $(this).data('id');
        var nama = $(this).data('nama');

        swal({
            title: "Apakah anda yakin?",
            text: "Untuk menghapus data : " + nama,
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              $.ajax({
                type: 'ajax',
                method: 'get',
                url: '/menu/delete/' + kode,
                async: true,
                dataType: 'json',
                success: function(response) {
                  console.log(response.status);

                  if (response.status == true) {
                    swal({
                        title: "Success!",
                        text: response.pesan,
                        icon: "success"
                      })
                      .then(function() {
                        location.reload(true);
                      });
                  } else {
                    swal("Hapus Data Gagal!", {
                      icon: "warning",
                      title: "Failed!",
                      text: response.pesan,
                    });
                  }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                  var err = eval("(" + jqXHR.responseText + ")");
                  swal("Error!", err.Message, "error");
                }
              });
            } else {
              swal("Dibatalkan!", "Hapus Data Dibatalkan.", "error");
            }
          });
      });
    });
  </script>
@endpush

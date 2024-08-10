@extends('layouts.app')

@push('after-style')
  <style>
    .sortable>li>div {
      margin-bottom: 10px;
      border-bottom: 1px solid #ddd;
    }

    .sortable,
    .sortable>li>div {
      display: block;
      width: 100%;
      float: left;
    }

    .sortable>li {
      display: block;
      width: 100%;
      margin-bottom: 5px;
      float: left;
      border: 1px solid #ddd;
      background: #fff;
      padding: 5px;
    }

    .sortable ul {
      padding: 5px;
    }
  </style>
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
            <ul class="sortable list-unstyled" id="sortable">
              @foreach ($data as $menu)
                @php
                  $id_menu = get_menu_id('menu');

                  //selalu bisa
                  $detailButton = '<a class="" href="' . route('menu.show', $menu->id) . '">Detail</a>';
                  $editButton = '';
                  if (isAccess('update', $id_menu, auth()->user()->role_id)) {
                      $editButton = '<a href="' . route('menu.edit', $menu->id) . '" class="">Edit</a>';
                  }
                  $deleteButton = '';
                  if (isAccess('delete', $id_menu, auth()->user()->role_id)) {
                      $deleteButton =
                          '<a class="btn-delete" href="javascript:void(0)" data-id="' .
                          $menu->id .
                          '" data-nama="' .
                          $menu->name .
                          '">Hapus</a>';
                  }
                  $action =
                      '
                              ' .
                      $editButton .
                      '
                              ' .
                      $detailButton .
                      '
                              ' .
                      $deleteButton .
                      '
                              ';
                @endphp
                <li id="mdl-{{ $menu->id }}">
                  <div class="block block-title">
                    <i class="fa fa-sort"></i>
                    {{ $menu->name }}
                    {!! $action !!}
                  </div>

                  <ul class="sortable list-unstyled">
                    @foreach ($menu->menus as $submenu)
                      @php
                        $id = get_menu_id('menu');

                        //selalu bisa
                        $detailButton = '<a class="" href="' . route('menu.show', $submenu->id) . '">Detail</a>';
                        $editButton = '';
                        if (isAccess('update', $id, auth()->user()->role_id)) {
                            $editButton = '<a href="' . route('menu.edit', $submenu->id) . '" class="">Edit</a>';
                        }
                        $deleteButton = '';
                        if (isAccess('delete', $id, auth()->user()->role_id)) {
                            $deleteButton =
                                '<a class="btn-delete" href="javascript:void(0)" data-id="' .
                                $submenu->id .
                                '" data-nama="' .
                                $submenu->name .
                                '">Hapus</a>';
                        }
                        $action =
                            '
                                      ' .
                            $editButton .
                            '
                                      ' .
                            $detailButton .
                            '
                                      ' .
                            $deleteButton .
                            '
                                      ';
                      @endphp
                      <li id="mdl-{{ $submenu->id }}">
                        <div class="block block-title"><i class="fa fa-sort"></i> {{ $submenu->name }}
                          {!! $action !!}</div>
                        <ul class="sortable list-unstyled"></ul>
                      </li>
                    @endforeach
                  </ul>
                  <!-- /.menu-sortable -->
                </li>
              @endforeach
            </ul>
            <!-- /.menu-sortable -->

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('after-script')
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script>
    $(document).ready(function() {
      $('.sortable').sortable({
        connectWith: '.sortable',
        placeholder: 'placeholder',
        // sort: function(e) {
        //     console.log('Handled');
        //     $(".sortable").css("background", "yellow");
        // },
        update: function(event, ui) {
          var struct = [];
          var i = 0;
          $(".sortable").each(function(ind, el) {
            struct[ind] = {
              index: i,
              class: $(el).attr("class"),
              count: $(el).children().length,
              parent: $(el).parent().is("li") ? $(el).parent().attr("id") : "",
              parentIndex: $(el).parent().is("li") ? $(el).parent().index() : "",
              array: $(el).sortable("toArray"),
              serial: $(el).sortable("serialize")
            };
            i++;
          });

          var orderData = {};
          $(struct).each(function(k, v) {
            var main = v.array[0];
            orderData[v.parent] = v.array;
          });

          // var myJsonString = JSON.stringify(orderData);
          // console.log(myJsonString);

          $.ajax({
            url: "menu/sort",
            method: "POST",
            data: {
              'main': orderData,
              '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
              // alert('Data berhasil diperbarui');
            }
          });
        }
      }).disableSelection();

      //delete
      $('#sortable').on('click', '.btn-delete', function() {
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

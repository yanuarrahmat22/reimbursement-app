@extends('layouts.app')

@push('after-style')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush

@section('title')
  Data Pengguna
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('home') }}">Dashboard</a>
        </li>

        <li class="breadcrumb-item active">Daftar Pengguna</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengguna</h5>

            @if (isAccess('create', $get_menu, auth()->user()->role_id))
              <a href="{{ route('user.create') }}">
                <button type="button" class="btn btn-primary btn-icon-text">
                  <i class="fa fa-plus btn-icon-prepend"></i>
                  Tambah
                </button>
              </a>
            @endif
          </div>

          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-12 float-left">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label>Cari berdasarkan kapan dibuat:</label>
                    <input type="text" class="form-control" name="datetime_created_at" id="datetime_created_at"
                      readonly>

                    <input type="hidden" class="form-control" name="startdatetime_created_at"
                      id="startdatetime_created_at" readonly>
                    <input type="hidden" class="form-control" name="enddatetime_created_at" id="enddatetime_created_at"
                      readonly>
                  </div>

                  <div class="col-md-4 mb-3 d-flex align-items-end">
                    <a href="javascript:void(0)" class="btn btn-secondary btn-icon-text btn-clear-filter">
                      Clear Filter
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="table-responsive text-nowrap">
              <table class="table" id="table-daftar-pengguna" style="width: 100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>NIP</th>
                    <th>Hak Akses</th>
                    <th>Actions</th>
                  </tr>
                </thead>

                @if (isAccess('read', $get_menu, auth()->user()->role_id))
                  <tbody class="table-border-bottom-0">
                  </tbody>
                @endif
              </table>
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
    $(function() {
      var date = moment.utc().format();

      $('input[name="set_datetime_created_at"]').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        showDropdowns: true,
        minYear: 2000,
        maxYear: parseInt(moment().format('YYYY'), 10),
        locale: {
          format: 'Y-MM-DD H:mm', // pasang jika pake startdate dan enddate
        }
      }, function(start, end, label) {
        $('#request_datetime_created_at').val(start.format('YYYY-MM-DD H:mm'));
      });

      $('input[name="datetime_created_at"]').daterangepicker({
        timePicker: true,
        timePicker24Hour: true,
        // startDate: moment().subtract(1, "days"), // pasang jika mau ada default
        // endDate: moment(), // pasang jika mau ada default
        autoUpdateInput: false, // pasang jika mau tidak ada default isian
        locale: {
          //format: 'DD/MM/Y H:mm' // pasang jika pake startdate dan enddate
          cancelLabel: 'Clear' // pasang jika mau tidak ada default isian
        }
      });

      $('input[name="datetime_created_at"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('D MMMM YYYY H:mm') + ' - ' + picker.endDate.format(
          'D MMMM YYYY H:mm'));

        // console.log(picker.startDate.format('YYYY-MM-DD H:mm'));
        // console.log(picker.endDate.format('YYYY-MM-DD H:mm'));

        $('#startdatetime_created_at').val(picker.startDate.format('YYYY-MM-DD H:mm'));
        $('#enddatetime_created_at').val(picker.endDate.format('YYYY-MM-DD H:mm'));

        getStartDatetime()
        getEndDatetime()
        table.draw();
      });

      $('input[name="datetime_created_at"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        // console.log(picker.startDate.format('YYYY-MM-DD'));
        // console.log(picker.endDate.format('YYYY-MM-DD'));

        $('#startdatetime_created_at').val('');
        $('#enddatetime_created_at').val('');

        getStartDatetime()
        getEndDatetime()
        table.draw();
      });

      function getStartDatetime() {
        var startdatetime_created_at = $('#startdatetime_created_at').val();
        // console.log(startdatetime_created_at);
        return startdatetime_created_at;
      }

      function getEndDatetime() {
        var enddatetime_created_at = $('#enddatetime_created_at').val();
        // console.log(enddatetime_created_at);
        return enddatetime_created_at;
      }

      $('#datetime_created_at').change(function() {
        table.draw();
      })

      $('.btn-clear-filter').click(function() {
        $('#datetime_created_at').val('');
        $('#startdatetime_created_at').val('');
        $('#enddatetime_created_at').val('');

        table.ajax.reload();

        getStartDatetime()
        getEndDatetime()
      });

      var table = $('#table-daftar-pengguna').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        responsive: true,
        ajax: {
          url: "{{ route('user.index') }}",
          type: "GET",
          data: function(d) {
            d.search = $('input[type="search"]').val();
            d.startdatetime_created_at = getStartDatetime();
            d.enddatetime_created_at = getEndDatetime();
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'name',
            name: 'name'
          },
          {
            data: 'email',
            name: 'email'
          },
          {
            data: 'nip',
            name: 'nip'
          },
          {
            data: 'set_role',
            name: 'set_role'
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
          }
        ]
      });

      table.on('draw', function() {
        $('[data-toggle="tooltip"]').tooltip();
      });

      // datatables responsive
      new $.fn.dataTable.FixedHeader(table);

      //reset
      $('#table-daftar-pengguna').on('click', '.btn-reset', function() {
        var kode = $(this).data('id');
        var nama = $(this).data('nama');
        swal({
            title: "Apakah anda yakin?",
            text: "Untuk mereset password : " + nama,
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              $.ajax({
                type: 'ajax',
                method: 'get',
                url: '/user-reset/' + kode,
                async: true,
                dataType: 'json',
                success: function(response) {
                  if (response.status == true) {
                    var span = document.createElement("span");
                    span.innerHTML = "" + response.pesan + "";

                    swal({
                        html: true,
                        title: "Success!",
                        content: span,
                        icon: "success"
                      })
                      .then(function() {
                        location.reload(true);
                      });
                  } else {
                    var pesan = "";
                    var data_pesan = response.pesan;
                    const wrapper = document.createElement('div');

                    if (typeof(data_pesan) == 'object') {
                      jQuery.each(data_pesan, function(key, value) {
                        console.log(value);
                        pesan += value + ' <br>';
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
              });
            } else {
              swal("Cancelled", "Reset Password Dibatalkan.", "error");
            }
          });
      });

      //delete
      $('#table-daftar-pengguna').on('click', '.btn-hapus', function() {
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
                url: '/user-destroy/' + kode,
                async: true,
                dataType: 'json',
                success: function(response) {
                  if (response.status == true) {
                    var span = document.createElement("span");
                    span.innerHTML = "" + response.pesan + "";

                    swal({
                        html: true,
                        title: "Success!",
                        content: span,
                        icon: "success"
                      })
                      .then(function() {
                        location.reload(true);
                      });
                  } else {
                    swal("Hapus Data Gagal !", {
                      icon: "warning",
                    });
                  }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                  var err = eval("(" + jqXHR.responseText + ")");
                  swal("Error!", err.Message, "error");
                }
              });
            } else {
              swal("Cancelled", "Hapus Data Dibatalkan.", "error");
            }
          });
      });
    })
  </script>
@endpush

@extends('layouts.app')

@push('after-style')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

  <style>
    .dataTables_wrapper .dataTables_filter input:not(:valid):not(:focus) {
      box-shadow: 0 0 5px #fff !important;
    }

    .dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
      -webkit-appearance: none !important;
    }

    .dataTables_wrapper .dataTables_filter button {
      visibility: hidden;
      outline: none;
    }

    .dataTables_wrapper .dataTables_filter input:valid~button {
      visibility: visible;
    }
  </style>
@endpush

@section('title')
  Data Pengajuan Reimbursement
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('/home') }}">Dashboard</a>
        </li>

        <li class="breadcrumb-item active">Data Pengajuan Reimbursement</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header">
            <div class="row ms-2 me-3">
              <div
                class="col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                <h5 class="mb-0">Data Pengajuan Reimbursement</h5>
              </div>

              <div
                class="col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-2">
                <div class="ms-auto">
                  @if (isAccess('create', $get_menu, auth()->user()->role_id))
                    <a href="{{ route('reimbursement.create') }}" class="btn btn-primary btn-icon-text order-1">
                      <i class="fa fa-plus btn-icon-prepend"></i>
                      Buat Pengajuan
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-12 float-left">
                <div class="row mb-3">
                  <div class="col-md-6 mb-3">
                    <label>Filter tanggal pengajuan:</label>
                    <input type="text" class="form-control" name="datetime_created" id="datetime_created" readonly>

                    <input type="hidden" class="form-control" name="startdatetime_created" id="startdatetime_created"
                      readonly>
                    <input type="hidden" class="form-control" name="enddatetime_created" id="enddatetime_created"
                      readonly>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label>Filter status pengajuan:</label>

                    <select class="form-control" name="status" id="status" style="width: 100%">
                      <option value="">Pilih Status</option>
                      <option value="waiting">Menunggu</option>
                      <option value="rejected">Ditolak</option>
                      <option value="approved">Disetujui</option>
                      <option value="done">Selesai Dibayar</option>
                    </select>
                  </div>

                  <div class="col-md-12 mb-3 d-flex align-items-end">
                    <a href="javascript:void(0)" class="btn btn-secondary btn-icon-text btn-clear-filter">
                      Clear Filter
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-12">
                <div class="badge rounded-pill me-1 bg-label-warning">Menunggu</div>
                <div class="badge rounded-pill me-1 bg-label-danger">Ditolak</div>
                <div class="badge rounded-pill me-1 bg-label-success">Disetujui</div>
                <div class="badge rounded-pill me-1 bg-label-info">Selesai Dibayar</div>
              </div>
            </div>

            <div class="table-responsive text-nowrap">
              <table class="table" id="table-daftar-reimbursement" style="width: 100%">
                <thead>
                  <tr>
                    <th>No</th>

                    @if (isAccess('approval', $get_menu, auth()->user()->role_id))
                      <th>Nama Pembuat</th>
                      <th>Tanggal Diajukan</th>
                      <th>Nama Pengajuan</th>
                      <th>Deskripsi</th>
                      <th>Detail Status</th>

                      <th>Actions</th>
                    @else
                      <th>Tanggal Diajukan</th>
                      <th>Nama Pengajuan</th>
                      <th>Deskripsi</th>
                      <th>Detail Status</th>

                      <th>Actions</th>
                    @endif
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
  <script>
    $(function() {
      var date = moment.utc().format();

      $('input[name="datetime_created"]').daterangepicker({
        timePicker: true,
        timePicker24Hour: true,
        // startDate: moment().subtract(1, "days"), // set default value
        // endDate: moment(), // set default value
        autoUpdateInput: false, 
        locale: {
          cancelLabel: 'Clear'
        }
      });

      $('input[name="datetime_created"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('D MMMM YYYY H:mm') + ' - ' + picker.endDate.format(
          'D MMMM YYYY H:mm'));

        $('#startdatetime_created').val(picker.startDate.format('YYYY-MM-DD H:mm'));
        $('#enddatetime_created').val(picker.endDate.format('YYYY-MM-DD H:mm'));

        getStartDatetimeCreated()
        getEndDatetimeCreated()
        table.draw();
      });

      $('input[name="datetime_created"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');

        $('#startdatetime_created').val('');
        $('#enddatetime_created').val('');

        getStartDatetimeCreated()
        getEndDatetimeCreated()
        table.draw();
      });

      var columns;
      var is_access = "{{ isAccess('approval', $get_menu, auth()->user()->role_id) }}";

      if (is_access == true) {
        columns = [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'user_created',
            name: 'user_created'
          },
          {
            data: 'date_created',
            name: 'date_created'
          },
          {
            data: 'name_submission',
            name: 'name_submission'
          },
          {
            data: 'description',
            name: 'description'
          },
          {
            data: 'status_detail',
            name: 'status_detail'
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
          }
        ]
      } else {
        columns = [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'date_created',
            name: 'date_created'
          },
          {
            data: 'name_submission',
            name: 'name_submission'
          },
          {
            data: 'description',
            name: 'description'
          },
          {
            data: 'status_detail',
            name: 'status_detail'
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
          }
        ]
      }

      var table = $('#table-daftar-reimbursement').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        responsive: true,
        ajax: {
          url: "{{ url()->current() }}",
          type: "GET",
          data: function(d) {
            d.search = $('input[type="search"]').val();
            d.startdatetime_created = getStartDatetimeCreated();
            d.enddatetime_created = getEndDatetimeCreated();
            d.status = getStatus();
          }
        },
        columns: columns,
        createdRow: function(row, data, dataIndex) {
          if (data["status_color"] == "waiting") {
            $(row).css("background-color", "#fff2d6");
            $(row).addClass("warning");
          }

          if (data["status_color"] == "rejected") {
            $(row).css("background-color", "#ffe0db");
            $(row).addClass("danger");
          }

          if (data["status_color"] == "approved") {
            $(row).css("background-color", "#d2f3c2");
            $(row).addClass("success");
          }

          if (data["status_color"] == "done") {
            $(row).css("background-color", "#d7f5fc");
            $(row).addClass("info");
          }
        },
        initComplete: function(settings) {

        }
      });

      function getStartDatetimeCreated() {
        var startdatetime_created = $('#startdatetime_created').val();
        // console.log(startdatetime_created);
        return startdatetime_created;
      }

      function getEndDatetimeCreated() {
        var enddatetime_created = $('#enddatetime_created').val();
        // console.log(enddatetime_created);
        return enddatetime_created;
      }

      function getStatus() {
        var status = $('#status').val();
        return status;
      }

      $('#datetime_created').change(function() {
        table.draw();
      })

      $('#status').select2({
        placeholder: 'Pilih Status',
        theme: "bootstrap-5"
      });

      $('#status').change(function() {
        table.draw();
      })

      table.on('draw', function() {
        $('[data-toggle="tooltip"]').tooltip();
      });

      // datatables responsive
      new $.fn.dataTable.FixedHeader(table);

      $('.btn-clear-filter').click(function() {
        $('#status').val('').trigger('change');
        $('#datetime_created').val('');
        $('#startdatetime_created').val('');
        $('#enddatetime_created').val('');

        table.ajax.reload();

        getStartDatetimeCreated()
        getEndDatetimeCreated()
        getStatus()
      });

      // payment confirmation
      $('#table-daftar-reimbursement').on('click', '.btn-payment-confirmation', function() {
        var kode = $(this).data('id');
        var nama = $(this).data('nama');
        swal({
            title: "Apakah anda yakin?",
            text: "Untuk mengkonfirmasi pembayaran : " + nama,
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willConfirm) => {
            if (willConfirm) {
              $.ajax({
                type: 'ajax',
                method: 'get',
                url: '/reimbursement/payment-confirmation/' + kode,
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
              swal("Cancelled", "Konfirmasi Pembayaran Data Dibatalkan.", "error");
            }
          });
      });

      //delete
      $('#table-daftar-reimbursement').on('click', '.btn-hapus', function() {
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
                url: '/reimbursement/delete/' + kode,
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
              swal("Cancelled", "Hapus Data Dibatalkan.", "error");
            }
          });
      });
    });
  </script>
@endpush

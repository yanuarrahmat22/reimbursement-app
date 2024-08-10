@extends('layouts.app')

@push('after-style')
@endpush

@section('title')
  Detail Data Pengajuan Reimbursement
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

        <li class="breadcrumb-item active">Detail Data Pengajuan Reimbursement</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Data Pengajuan Reimbursement</h5>

            <a href="{{ route('reimbursement.index') }}">
              <button type="button" class="btn btn-secondary btn-icon-text">
                <i class="fas fa-arrow-left btn-icon-prepend"></i>
                Kembali
              </button>
            </a>
          </div>

          <div class="card-body">
            <div class="row">
              <!-- User Sidebar -->
              <div class="col-xl col-lg col-md order-0 order-md-0">
                <!-- User Card -->
                <div class="card mb-4">
                  <div class="card-body">
                    <div class="user-avatar-section">
                      <div class=" d-flex align-items-center flex-column">
                        @if ($item->usercreated->avatar != null)
                          @if (Storage::disk('public')->exists($item->usercreated->avatar))
                            <img src="{{ Storage::url($item->usercreated->avatar) }}" class="img-fluid rounded my-4"
                              height="110" width="110" alt="User avatar" />
                          @endif
                        @else
                          <img src="{{ asset('admin-assets/assets/img/avatars/user-default.png') }}" alt="user-avatar"
                            class="img-fluid rounded my-4" height="110" width="110" alt="User avatar" />
                        @endif

                        <div class="user-info text-center">
                          <h4 class="mb-2">{{ $item->usercreated->name }}</h4>
                          <span class="badge bg-label-secondary">Jabatan:
                            <strong>{{ $item->usercreated->role->name }}</strong></span>
                        </div>
                      </div>
                    </div>

                    <h5 class="pb-2 border-bottom mb-4">Details</h5>
                    <div class="info-container">
                      <ul class="list-unstyled">
                        <li class="mb-3">
                          <span class="fw-bold me-2">Tanggal Pengajuan:</span>
                          <span>{{ $item->date_created != null ? \Carbon\Carbon::createFromFormat('Y-m-d', $item->date_created)->isoFormat('D MMMM YYYY') : '-' }}</span>
                        </li>

                        <li class="mb-3">
                          <span class="fw-bold me-2">Nama Pengajuan:</span>
                          <span>{{ $item->name }}</span>
                        </li>

                        <li class="mb-3">
                          <span class="fw-bold me-2">Deskripsi:</span>
                          <span>{{ $item->description }}</span>
                        </li>

                        <li class="mb-3">
                          <span class="fw-bold me-2">File Pendukung:</span>
                          <span>
                            @if ($item->file != null)
                              @if (Storage::disk('public')->exists($item->file))
                                <div class="mb-2">
                                  <a href="{{ Storage::url($item->file) }}" class="btn btn-sm btn-dark" download>
                                    Download File Pendukung
                                  </a>
                                </div>
                              @endif
                            @else
                              Tidak Ada
                            @endif
                          </span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <!-- /User Card -->
              </div>
              <!--/ User Sidebar -->

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('after-script')
@endpush

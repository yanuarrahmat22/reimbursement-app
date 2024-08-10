@extends('layouts.app')

@push('after-style')
@endpush

@section('title')
  Detail Data Menu
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

        <li class="breadcrumb-item active">Detail Data Menu</li>
      </ol>
    </nav>

    <!-- Collapse -->
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Data Menu</h5>

            <a href="{{ route('menu.index') }}">
              <button type="button" class="btn btn-secondary btn-icon-text">
                <i class="fas fa-arrow-left btn-icon-prepend"></i>
                Kembali
              </button>
            </a>
          </div>

          {{-- @php
            dd($data->upid);
          @endphp --}}

          <div class="card-body">
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label" for="name">Parent Menu</label>
              <div class="col-sm-10">
                <label class="col-form-label">: {{ $data->upid == '0' ? 'Parent' : $data->menu->name }}</label>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label" for="name">Menu Name</label>
              <div class="col-sm-10">
                <label class="col-form-label">: {{ $data->name }}</label>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label" for="code">Menu Kode</label>
              <div class="col-sm-10">
                <label class="col-form-label">: {{ $data->code }}</label>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label" for="position">Posisi</label>
              <div class="col-sm-10">
                <label class="col-form-label">: {{ $data->position }}</label>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label" for="link">Menu Link</label>
              <div class="col-sm-10">
                <label class="col-form-label">: {{ $data->link }}</label>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label" for="icon">Icon</label>
              <div class="col-sm-10">
                <label class="col-form-label">: {{ $data->icon }}</label>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label" for="permission">Permission Menu</label>
              <div class="col-sm-10">
                <label class="col-form-label">: {{ $data->permission }}</label>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label" for="description">Description</label>
              <div class="col-sm-10">
                <label class="col-form-label">: {{ $data->description }}</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('after-script')
@endpush

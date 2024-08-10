@extends('layouts.app')

@push('after-style')
@endpush

@section('title')
  Home
@endsection

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-lg-12 mb-4 order-0">
        <div class="card">
          <div class="d-flex align-items-end row">
            <div class="col-sm-7">
              <div class="card-body">
                <h5 class="card-title text-primary">Hi, {{ Auth::user()->name }}! </h5>
                <p class="mb-4">
                  Welcome to reimbursement app!
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('after-script')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.0/dist/chart.min.js"></script>
  <script>
    $(document).ready(function() {
    });
  </script>
@endpush

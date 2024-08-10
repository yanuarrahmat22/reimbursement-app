@extends('layouts.auth')

@section('content')
  <h2 class="mb-2 text-center">Login</h2>
  <h4 class="mb-2 text-center">Reimbursement App</h4>

  <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label style="margin-left: 20px" for="nip" class="form-label">{{ __('NIP') }} </label>

      <input id="nip" type="text" class="form-control @error('nip') is-invalid @enderror" name="nip"
        value="{{ old('nip') }}" required autocomplete="nip" autofocus>

      @error('nip')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
    </div>

    <div class="mb-3 form-password-toggle">
        <div class="d-flex justify-content-between">
          <label style="margin-left: 20px" class="form-label" for="password">{{ __('Password') }}</label>

          {{-- <a href="/password/reset">
            <small>Forgot Password?</small>
          </a> --}}
        </div>

        <div class="input-group input-group-merge">
          <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password"
            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
            aria-describedby="password" autocomplete="current-password">

          <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>

        @error('password')
          <span class="invalid-feedback" role="alert" style="display: block !important">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
      </div>

    <div class="mb-3">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" type="checkbox" name="remember" id="remember"
          {{ old('remember') ? 'checked' : '' }} />
        <label class="form-check-label" for="remember-me"> {{ __('Remember Me') }} </label>
      </div>
    </div>

    <div class="mb-3">
      <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Login') }}</button>
    </div>
  </form>
@endsection

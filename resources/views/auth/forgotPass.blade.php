@extends('layout')
@section('page-title', 'Forgot Password')
@section('auth-content')
<div class="container register-container vh-100 mb-4 pb-4">

    <form method="POST" action="{{ route('register-form') }}" enctype="multipart/form-data">
        <div class="row" style="padding-top:3rem;">
            <div class="row">
                <div class="col">
                    <div class="text-start header-texts back-link-container">
                        <a href="{{ route('login-page') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
                        Reset Password.
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingInput" name="email" placeholder="Email">
                        <label for="floatingInput">Email Address</label>
                        @error('email')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingPassword" name="password" required placeholder="Password">
                        <label for="floatingPassword">Password</label>
                        @error('password')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingInput" name="password_confirmation" required placeholder="Confirm Password">
                        <label for="floatingPassword">Confirm Password</label>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-center align-items-center mb-3">
                <button type="submit" class="m-2 submit-btn border-0 bg-text">
                    Save Password
                </button>
            </div>

        </div>
    </form>
</div>

@endsection
@extends('layouts.welcome')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.17.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.17.2/firebase-messaging-compat.js"></script>

@section('content')
    @if (isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif
    <div class="card">
        <div class="card-header">
            <h3>{{ __('Login') }}</h3> <!-- Translated Login -->
        </div>
        <div class="card-body">
            <form action="{{ route('login') }}" method="post">
                @csrf
                <div class="form-group">
                    <input type="text" placeholder="{{ __('Email') }}" id="email" name="email" required autofocus>
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <input type="password" placeholder="{{ __('Password') }}" id="password" name="password" required>
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">{{ __('Remember Me') }}</label> <!-- Translated Remember Me -->
                </div>

                <button type="submit" class="btn4" id="login-button">{{ __('Signin') }}</button> <!-- Translated Signin -->
                <label for="remember">{{ __("Don't have an account?") }}</label> <!-- Translated Don't have an account -->
                <a href="{{ route('register.form') }}" >{{ __('Sign Up') }}</a> <!-- Translated Sign Up -->
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/main2.js') }}"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
@endsection

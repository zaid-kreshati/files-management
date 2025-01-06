@extends('layouts.welcome')
@section('content')
    @if (isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Login') }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
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
                            <a for="remember">{{ __('Remember Me') }}</a>
                        </div>

                        <button type="submit" class="btn4">{{ __('Signin') }}</button>
                        <label for="remember">{{ __('Don\'t have an account?') }}</label>
                        <a href="{{ route('register.form') }}"  >{{ __('Sign UP') }}</a>
                    </form>
                </div>
            </div>
@endsection

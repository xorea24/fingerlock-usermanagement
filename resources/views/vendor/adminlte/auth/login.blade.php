@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Smooth transitions for the AdminLTE box */
        .login-card-body { border-radius: 1.5rem; }
        .card { border-radius: 1.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important; }
        .input-group-text { border-radius: 0 0.75rem 0.75rem 0 !important; }
        .form-control { border-radius: 0.75rem 0 0 0.75rem !important; }
    </style>
@stop

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
@endphp

@section('auth_header')
    <div class="text-center">
        <h4 class="font-black text-slate-800 uppercase tracking-tight mb-1">{{ __('adminlte::adminlte.login_message') }}</h4>
        <p class="text-xs text-slate-400">Enter your credentials to manage the gallery</p>
    </div>
@stop

@section('auth_body')
    {{-- Unified Public Access Button --}}
    <div class="mb-4">
        <a href="/gallery" class="w-full flex items-center justify-center gap-2 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-600 text-sm hover:bg-slate-100 hover:text-slate-900 transition-all duration-200">
            <i class="fas fa-arrow-left text-xs"></i>
            Back to Public Gallery
        </a>
    </div>

    <div class="relative flex items-center py-3">
        <div class="flex-grow border-t border-gray-100"></div>
        <span class="flex-shrink mx-4 text-[10px] font-black text-gray-300 uppercase tracking-widest">OR ADMIN LOGIN</span>
        <div class="flex-grow border-t border-gray-100"></div>
    </div>

    <form action="{{ $loginUrl }}" method="post" class="mt-4">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope text-slate-400"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-4">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock text-slate-400"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="row items-center">
            <div class="col-7">
                <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="text-sm text-slate-500 font-medium">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>
            </div>
            <div class="col-5">
                <button type="submit" class="btn btn-primary btn-block rounded-xl font-bold shadow-md shadow-blue-100 py-2">
                    <span class="fas fa-sign-in-alt mr-1"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    <div class="mt-4 text-center">
        @if($passResetUrl)
            <p class="mb-1">
                <a href="{{ $passResetUrl }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                    {{ __('adminlte::adminlte.i_forgot_my_password') }}
                </a>
            </p>
        @endif

        @if($registerUrl)
            <p class="mb-0">
                <span class="text-xs text-slate-400">New here?</span>
                <a href="{{ $registerUrl }}" class="text-xs font-bold text-slate-700 hover:underline">
                    {{ __('adminlte::adminlte.register_a_new_membership') }}
                </a>
            </p>
        @endif
    </div>
@stop
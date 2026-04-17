@extends('adminlte::auth.auth-page', ['authType' => 'login'])

{{-- 
    ===========================================================================
    CUSTOM LOGIN PAGE FOR MAYOR'S OFFICE UPLOADING SYSTEM (FINGERLOCK)
    ===========================================================================
    This file overrides the default AdminLTE login view. It features a custom
    styled card, customized form controls, and branding specific to the 
    Mayor's Office Uploading System. It utilizes Tailwind CSS for some utility 
    classes alongside the custom inline styles defined below.
    ===========================================================================
--}}

{{-- Hide the default AdminLTE logo above the card --}}
@section('logo')
@stop

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Hide the default top logo */
        .login-logo { display: none !important; }

        /* Card styling */
        .card {
            border-radius: 1.25rem !important;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15) !important;
            border: none !important;
            overflow: hidden;
        }
        .login-card-body { padding: 2rem !important; }

        /* Inputs */
        .form-control {
            border-radius: 0.6rem 0 0 0.6rem !important;
            border: 1.5px solid #e2e8f0 !important;
            height: 44px;
        }
        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: none !important;
        }
        .input-group-text {
            border-radius: 0 0.6rem 0.6rem 0 !important;
            border: 1.5px solid #e2e8f0 !important;
            border-left: none !important;
            background: #f8fafc !important;
        }

        /* Sign in button */
        .btn-primary {
            border-radius: 0.6rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.03em;
        }

        /* Back button */
        .btn-outline-secondary {
            border-radius: 0.6rem !important;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Divider */
        .divider-text {
            font-size: 10px;
            letter-spacing: 0.15em;
            color: #cbd5e1;
            font-weight: 700;
        }
    </style>
@stop

@php
    $loginUrl    = View::getSection('login_url')          ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url')       ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl     = $loginUrl     ? route($loginUrl)     : '';
        $registerUrl  = $registerUrl  ? route($registerUrl)  : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $loginUrl     = $loginUrl     ? url($loginUrl)     : '';
        $registerUrl  = $registerUrl  ? url($registerUrl)  : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
@endphp

{{-- ─── Card Header: Logo + Title ─── --}}
@section('auth_header')
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding-top: 1rem; padding-bottom: 0.5rem;">
        <img src="{{ asset('vendor/adminlte/dist/img/stc_logo5.png') }}"
             alt="Mayor's Office Logo"
             style="width: 72px; height: 72px; object-fit: contain; display: block; margin: 0 auto;
                    filter: drop-shadow(0 3px 6px rgba(0,0,0,0.12));">
        <h5 style="margin-top: 0.75rem; margin-bottom: 0; font-weight: 900; color: #1e293b; text-transform: uppercase; letter-spacing: -0.02em;">
            Mayor's <span style="color: #3b82f6;">Office</span>
        </h5>
        <p style="font-size: 11px; letter-spacing: 0.15em; text-transform: uppercase; color: #94a3b8; margin-bottom: 0;">
            Uploading System
        </p>
        <hr style="width: 100%; border-color: #f1f5f9; margin-top: 1rem; margin-bottom: 0;">
    </div>
@stop

{{-- ─── Card Body: Form ─── --}}
@section('auth_body')
    {{-- Back to Slideshow --}}
    

    {{-- Divider --}}
    <div class="d-flex align-items-center my-3">
        <div class="flex-grow-1" style="border-top: 1px solid #f1f5f9;"></div>
        <span class="mx-3 divider-text">SIGN IN</span>
        <div class="flex-grow-1" style="border-top: 1px solid #f1f5f9;"></div>
    </div>

    {{-- Login Form --}}
    <form action="{{ $loginUrl }}" method="post">
        @csrf

        {{-- Email --}}
        <div class="input-group mb-3">
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="{{ __('adminlte::adminlte.email') }}"
                   autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope text-muted" style="font-size:13px;"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="input-group mb-4">
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock text-muted" style="font-size:13px;"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Remember + Submit --}}
        <div class="row align-items-center">
            <div class="col-6">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="text-muted" style="font-size:13px;">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop

{{-- ─── Card Footer ─── --}}
@section('auth_footer')
    @if($passResetUrl)
        <div class="text-center py-2">
            <a href="{{ $passResetUrl }}"
               class="text-muted"
               style="font-size: 12px; text-decoration: none;">
                <i class="fas fa-key mr-1"></i>
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </div>
    @endif
@stop
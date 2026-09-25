@extends('layouts.app')

@section('title', 'Log in - ANotes')

@section('content')
<div class="auth-page"><div class="auth-layout"><aside class="auth-aside"><a class="app-brand" href="{{ route('home') }}"><span class="brand-mark">A</span><span>ANotes</span></a><div class="auth-aside-copy"><h1>Your ideas deserve a clear place.</h1><p>Return to your notes, reminders, and next steps in one calm workspace.</p></div><div class="auth-aside-foot">Made for clearer thinking.</div></aside><section class="auth-card"><span class="eyebrow">Welcome back</span><h2>Log in to ANotes</h2><p class="auth-subtitle">Pick up exactly where you left off.</p>@if (session('success'))<div class="alert alert-success alert-dismissible fade show mb-4" role="alert"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
			<form action="{{ route('login') }}" method="POST">@csrf
				<div class="form-group"><label for="email" class="form-label">Email address</label><input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
				<div class="form-group"><label for="password" class="form-label">Password</label><input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
				<button type="submit" class="btn btn-primary mt-2">Log in <i class="bi bi-arrow-right ms-2"></i></button>
			</form><p class="auth-footer-link">Do not have an account? <a href="{{ route('register') }}">Create one</a></p>
		</section></div></div>
@endsection

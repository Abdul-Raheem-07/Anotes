@extends('layouts.app')

@section('title', 'Create account - ANotes')

@section('content')
<div class="auth-page"><div class="auth-layout"><aside class="auth-aside"><a class="app-brand" href="{{ route('home') }}"><span class="brand-mark">A</span><span>ANotes</span></a><div class="auth-aside-copy"><h1>Make space for what matters.</h1><p>Build a simple daily rhythm around the notes and reminders you want to keep close.</p></div><div class="auth-aside-foot">A lighter way to stay organized.</div></aside><section class="auth-card"><span class="eyebrow">Start fresh</span><h2>Create your ANotes account</h2><p class="auth-subtitle">Your personal productivity space is a few details away.</p>
			<form action="{{ route('register') }}" method="POST">@csrf
				<div class="form-group"><label for="name" class="form-label">Full name</label><input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
				<div class="form-group"><label for="email" class="form-label">Email address</label><input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
				<div class="row g-3"><div class="col-md-6"><div class="form-group"><label for="password" class="form-label">Password</label><input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div><div class="col-md-6"><div class="form-group"><label for="password_confirmation" class="form-label">Confirm password</label><input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required></div></div></div>
				<button type="submit" class="btn btn-primary mt-2">Create account <i class="bi bi-arrow-right ms-2"></i></button>
			</form><p class="auth-footer-link">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
		</section></div></div>
@endsection

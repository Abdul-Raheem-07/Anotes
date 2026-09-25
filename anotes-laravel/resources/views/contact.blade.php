@extends('layouts.app')

@section('title', 'Contact ANotes')

@section('content')
<div class="container public-page">
    <div class="contact-layout">
        <div class="contact-intro"><span class="eyebrow"><i class="bi bi-chat-square-text me-1"></i>We are listening</span><h1>Have something<br>to share?</h1><p>Have a question, found a bug, or want to collaborate? Send a message below, or reach out directly.</p><div class="contact-detail"><i class="bi bi-envelope-fill"></i><a href="mailto:dotabdulraheemofficial07@gmail.com" class="text-decoration-none">dotabdulraheemofficial07@gmail.com</a></div></div>
        <div class="contact-form-card">
            <h2>Send a message</h2><p>We will get back to you as soon as possible.</p>
            @if (session('success'))<div class="alert alert-success alert-dismissible fade show mb-4" role="alert"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
            @if ($errors->any())<div class="alert alert-warning mb-4" role="alert"><strong>Please check the form.</strong><ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf
                <div class="row g-3"><div class="col-md-6"><label for="name" class="form-label">Your name</label><input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6"><label for="email" class="form-label">Email address</label><input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-12"><label for="subject" class="form-label">Subject</label><input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>@error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-12"><label for="message" class="form-label">Message</label><textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-send me-2"></i>Send Message</button>
            </form>
        </div>
    </div>
</div>
@endsection

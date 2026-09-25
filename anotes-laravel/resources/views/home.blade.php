@extends('layouts.app')

@section('title', 'ANotes - Clearer thinking, one note at a time')

@section('content')
<div class="container public-page">
    <section class="public-hero">
        <div><span class="eyebrow"><i class="bi bi-sparkles me-1"></i>Your calm productivity space</span><h1>Make room for<br><em>better thinking.</em></h1><p class="public-hero-copy">ANotes brings notes, tasks, and timely reminders into one simple place, so your next good idea is never far away.</p><div class="public-hero-actions"><a href="{{ route('register') }}" class="btn btn-primary btn-lg">Start organizing <i class="bi bi-arrow-up-right ms-2"></i></a><a href="{{ route('about') }}" class="btn btn-ghost btn-lg">See how it works</a></div></div>
        <div class="public-hero-note"><div class="hero-note-top"><span>Today's thought</span><i class="bi bi-journal-heart"></i></div><h2>Small steps become a clear day.</h2><p>Capture what matters, decide what comes next, and give each task its moment.</p><div class="hero-note-footer"><small><i class="bi bi-clock me-1"></i>Saved just now</small><span class="hero-check"><i class="bi bi-check2"></i></span></div></div>
    </section>

    <section class="public-section"><div class="public-section-heading"><span class="eyebrow">Everything in its place</span><h2>A lighter way to stay on top of things.</h2><p>Simple tools with enough structure to help you focus without getting in the way.</p></div><div class="public-feature-grid"><article class="public-feature"><div class="public-feature-icon"><i class="bi bi-journal-text"></i></div><h3>Capture ideas</h3><p>Write, edit, and keep personal notes organized in a distraction-free workspace.</p></article><article class="public-feature"><div class="public-feature-icon"><i class="bi bi-calendar2-check"></i></div><h3>Plan with intention</h3><p>Set one-off or repeating reminders so important tasks have a clear place on your day.</p></article><article class="public-feature"><div class="public-feature-icon"><i class="bi bi-bell"></i></div><h3>Stay in the loop</h3><p>Receive browser notifications when your scheduled reminders are ready for attention.</p></article></div></section>

    <section class="public-cta"><div><h2>Ready to clear some headspace?</h2><p>Create your free workspace and make your next thought count.</p></div><a href="{{ route('register') }}" class="btn btn-primary">Create your account</a></section>
</div>
@endsection

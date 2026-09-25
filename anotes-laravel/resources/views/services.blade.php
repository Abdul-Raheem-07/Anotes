@extends('layouts.app')

@section('title', 'What ANotes Offers')

@section('content')
<div class="container public-page">
    <section class="public-section pb-3"><div class="public-section-heading"><span class="eyebrow">The toolkit</span><h1 class="public-hero h-auto d-block py-0" style="min-height: 0; font-size: clamp(2.3rem, 5vw, 4rem);">Everything you need to<br><em>keep moving.</em></h1><p>Focused tools for the ideas, tasks, and reminders that make up a real day.</p></div></section>
    <section class="public-section pt-2"><div class="public-feature-grid"><article class="public-feature"><div class="public-feature-icon"><i class="bi bi-journal-text"></i></div><h3>Notes</h3><p>Add, edit, and manage notes instantly. Keep your thoughts organized in one safe location.</p><a href="{{ route('register') }}" class="section-link d-inline-block mt-4">Start a note <i class="bi bi-arrow-up-right"></i></a></article><article class="public-feature"><div class="public-feature-icon"><i class="bi bi-calendar2-check"></i></div><h3>Reminders</h3><p>Schedule one-off or repeating reminders with clear notifications when they are due.</p><a href="{{ route('register') }}" class="section-link d-inline-block mt-4">Plan your day <i class="bi bi-arrow-up-right"></i></a></article><article class="public-feature"><div class="public-feature-icon"><i class="bi bi-trash3"></i></div><h3>Recovery</h3><p>Deleted notes and reminders stay in Trash for seven days, giving you time to restore them.</p><a href="{{ route('about') }}" class="section-link d-inline-block mt-4">Learn more <i class="bi bi-arrow-up-right"></i></a></article></div></section>
    <section class="public-cta"><div><h2>Make your next step easier to find.</h2><p>Bring the important pieces of your day into one focused workspace.</p></div><a href="{{ route('register') }}" class="btn btn-primary">Get started</a></section>
</div>
@endsection

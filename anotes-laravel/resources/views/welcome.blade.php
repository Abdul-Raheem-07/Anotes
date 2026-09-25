@extends('layouts.app')

@section('title', 'ANotes')

@section('content')
<div class="container public-page">
    <section class="public-section text-center">
        <div class="public-section-heading mx-auto">
            <span class="eyebrow">ANotes</span>
            <h1>Make room for clearer thinking.</h1>
            <p>Capture ideas, organize tasks, and keep important reminders close.</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">Visit home</a>
        </div>
    </section>
</div>
@endsection

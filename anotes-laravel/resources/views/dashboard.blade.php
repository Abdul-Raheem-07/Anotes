@extends('layouts.app')

@section('title', 'Dashboard - ANotes')

@section('content')
@php
    $userNotes = auth()->user()->notes()->orderByDesc('tstamp')->get();
    $userReminders = auth()->user()->reminders()->orderBy('remind_date')->orderBy('remind_time')->get();
    $activeReminders = $userReminders->where('is_done', false);
    $completedReminders = $userReminders->where('is_done', true);
    $todayReminders = $activeReminders->filter(fn ($reminder) => optional($reminder->remind_date)->isToday());
    $upcomingReminders = $activeReminders->take(4);
@endphp

<div class="dashboard-page page-frame">
    <div class="page-heading dashboard-heading">
        <div>
            <span class="eyebrow"><i class="bi bi-stars me-1"></i>Personal overview</span>
            <h2>Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}</h2>
            <p>Here is what is happening with your productivity today.</p>
        </div>
        <a href="{{ route('notes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>New Note</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon stat-icon-green"><i class="bi bi-journal-text"></i></div><div><span>Total notes</span><strong>{{ $userNotes->count() }}</strong><small>Ideas captured</small></div></div>
        <div class="stat-card"><div class="stat-icon stat-icon-blue"><i class="bi bi-calendar2-check"></i></div><div><span>Active reminders</span><strong>{{ $activeReminders->count() }}</strong><small>Still on your list</small></div></div>
        <div class="stat-card"><div class="stat-icon stat-icon-amber"><i class="bi bi-sun"></i></div><div><span>Due today</span><strong>{{ $todayReminders->count() }}</strong><small>Keep an eye on these</small></div></div>
        <div class="stat-card"><div class="stat-icon stat-icon-violet"><i class="bi bi-check2-circle"></i></div><div><span>Completed</span><strong>{{ $completedReminders->count() }}</strong><small>Tasks checked off</small></div></div>
    </div>

    <div class="dashboard-grid">
        <section class="dashboard-section">
            <div class="section-heading"><div><span class="eyebrow">Your library</span><h3>Recent notes</h3></div><a href="{{ route('notes.index') }}" class="section-link">View all <i class="bi bi-arrow-up-right"></i></a></div>
            @if ($userNotes->isEmpty())
                <div class="empty-state compact"><i class="bi bi-journal-plus"></i><h4>Start your note library</h4><p>Capture an idea, plan, or thought in a few words.</p><a href="{{ route('notes.create') }}" class="btn btn-primary btn-sm">Create a note</a></div>
            @else
                <div class="dashboard-notes-list">
                    @foreach ($userNotes->take(3) as $note)
                        <article class="dashboard-note-row"><div class="note-accent"></div><div class="grow"><h4>{{ $note->title }}</h4><p>{{ \Illuminate\Support\Str::limit($note->description, 100) }}</p><small><i class="bi bi-clock me-1"></i>{{ $note->tstamp }}</small></div><a href="{{ route('notes.edit', $note) }}" class="icon-button" title="Edit note" aria-label="Edit {{ $note->title }}"><i class="bi bi-arrow-up-right"></i></a></article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="dashboard-section">
            <div class="section-heading"><div><span class="eyebrow">Stay on track</span><h3>Upcoming reminders</h3></div><a href="{{ route('reminders.index') }}" class="section-link">View all <i class="bi bi-arrow-up-right"></i></a></div>
            @if ($upcomingReminders->isEmpty())
                <div class="empty-state compact"><i class="bi bi-calendar-plus"></i><h4>Your schedule is clear</h4><p>Add a reminder when something needs your attention.</p><a href="{{ route('reminders.create') }}" class="btn btn-primary btn-sm">Add reminder</a></div>
            @else
                <div class="dashboard-reminders-list">
                    @foreach ($upcomingReminders as $reminder)
                        <article class="dashboard-reminder-row"><div class="reminder-date"><strong>{{ optional($reminder->remind_date)->format('d') }}</strong><span>{{ optional($reminder->remind_date)->format('M') }}</span></div><div class="grow"><h4>{{ $reminder->title }}</h4><small><i class="bi bi-clock me-1"></i>{{ substr((string) $reminder->remind_time, 0, 5) }} <span class="mx-1">·</span> {{ ucfirst($reminder->repeat_option ?? 'none') }}</small></div><span class="status-dot" aria-label="Pending"></span></article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <section class="quick-actions"><div><span class="eyebrow">Shortcuts</span><h3>Make space for good ideas</h3></div><div class="quick-action-links"><a href="{{ route('notes.create') }}"><i class="bi bi-journal-plus"></i><span>New note</span><i class="bi bi-arrow-up-right"></i></a><a href="{{ route('reminders.create') }}"><i class="bi bi-calendar-plus"></i><span>New reminder</span><i class="bi bi-arrow-up-right"></i></a><a href="{{ route('notes.index') }}"><i class="bi bi-collection"></i><span>Browse notes</span><i class="bi bi-arrow-up-right"></i></a></div></section>
</div>
@endsection

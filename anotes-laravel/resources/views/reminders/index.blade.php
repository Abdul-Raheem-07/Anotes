@extends('layouts.app')

@section('title', 'My Reminders - ANotes')

@section('content')
@php
    $today = now()->toDateString();
    $todayReminders = $reminders->filter(fn ($reminder) => (optional($reminder->remind_date)->toDateString() ?? (string) $reminder->remind_date) === $today && !$reminder->is_done);
    $upcomingReminders = $reminders->filter(fn ($reminder) => (optional($reminder->remind_date)->toDateString() ?? (string) $reminder->remind_date) > $today && !$reminder->is_done);
    $completedReminders = $reminders->filter(fn ($reminder) => $reminder->is_done);
@endphp

<div class="container my-5 page-frame reminders-page">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif
    <div id="notifBlockedBanner" class="alert alert-warning alert-dismissible fade show mb-4 d-none" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>Browser notifications are currently blocked. Please update your browser site settings to receive desktop notifications when reminders are due.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>

    <div class="page-heading">
        <div><span class="eyebrow"><i class="bi bi-calendar2-check me-1"></i>Personal schedule</span><h2>Reminders</h2><p>Give important things a time and let ANotes keep them close.</p></div>
        <div class="page-heading-actions"><button id="enableNotifsBtn" type="button" class="btn btn-outline-primary btn-sm"><i class="bi bi-bell me-1"></i>Enable notifications</button><a href="{{ route('reminders.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>New Reminder</a></div>
    </div>

    @if ($reminders->isEmpty())
        <div class="empty-state"><i class="bi bi-calendar-plus"></i><h4>Your schedule is clear</h4><p>You have not created any reminders yet. Add one when something needs your attention.</p><a href="{{ route('reminders.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add your first reminder</a></div>
    @else
        <div class="reminder-groups">
            <section class="reminder-group"><div class="section-heading"><div><span class="eyebrow">{{ now()->format('l, M j') }}</span><h3>Today <span class="count-badge">{{ $todayReminders->count() }}</span></h3></div></div>@if ($todayReminders->isEmpty())<div class="section-empty"><i class="bi bi-check2-circle"></i><span>Nothing due today.</span></div>@else<div class="reminder-list">@foreach ($todayReminders as $reminder) @include('reminders._card', ['reminder' => $reminder]) @endforeach</div>@endif</section>
            <section class="reminder-group"><div class="section-heading"><div><span class="eyebrow">Next on your list</span><h3>Upcoming <span class="count-badge">{{ $upcomingReminders->count() }}</span></h3></div></div>@if ($upcomingReminders->isEmpty())<div class="section-empty"><i class="bi bi-calendar-check"></i><span>No upcoming reminders.</span></div>@else<div class="reminder-list">@foreach ($upcomingReminders as $reminder) @include('reminders._card', ['reminder' => $reminder]) @endforeach</div>@endif</section>
            <section class="reminder-group completed-group"><div class="section-heading"><div><span class="eyebrow">A little progress</span><h3>Completed <span class="count-badge">{{ $completedReminders->count() }}</span></h3></div></div>@if ($completedReminders->isEmpty())<div class="section-empty"><i class="bi bi-inbox"></i><span>Completed reminders will appear here.</span></div>@else<div class="reminder-list">@foreach ($completedReminders as $reminder) @include('reminders._card', ['reminder' => $reminder]) @endforeach</div>@endif</section>
        </div>
    @endif
</div>

<!-- In-App Reminder Alert Modal -->
<div class="modal fade" id="reminderAlertModal" tabindex="-1" aria-labelledby="reminderAlertModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content notification-modal"><div class="modal-header border-0"><h5 class="modal-title fw-bold" id="reminderAlertModalLabel"><i class="bi bi-bell-fill me-2"></i>Reminder alert</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body p-4 text-center"><i class="bi bi-alarm-fill notification-icon mb-3 d-block"></i><h4 class="fw-bold text-dark mb-2" id="reminderAlertTitle">Reminder Title</h4><p class="text-muted fs-6 mb-3" id="reminderAlertDesc">Reminder Description</p><div class="badge bg-light text-dark p-2 fs-6 border"><i class="bi bi-clock me-1"></i> Scheduled for: <span id="reminderAlertTime">--:--</span></div></div><div class="modal-footer border-0 justify-content-center"><button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">Dismiss</button></div></div></div></div>

<!-- In-App Toast Container Fallback -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;"><div id="reminderToast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body"><strong id="reminderToastTitle" class="me-2">Reminder</strong><span id="reminderToastBody">Details</span></div><button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div></div>

<script type="application/json" id="anotes-reminders-data">@json($remindersData ?? [])</script>
<script src="{{ asset('js/reminders.js') }}"></script>
@endsection

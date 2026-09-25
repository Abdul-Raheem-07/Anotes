@extends('layouts.app')

@section('title', 'Trash - ANotes')

@section('content')
<div class="container my-5 page-frame trash-page">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif

    <div class="page-heading">
        <div><span class="eyebrow"><i class="bi bi-archive me-1"></i>Personal archive</span><h2>Trash</h2><p>Deleted notes and reminders stay here for 7 days before being permanently removed.</p></div>
    </div>
    <div class="trash-retention"><i class="bi bi-clock-history"></i><span>Items are automatically cleared after seven days. Restore anything you still need before then.</span></div>

    <section class="trash-section">
        <div class="section-heading"><div><span class="eyebrow">Written thoughts</span><h3>Deleted notes <span class="count-badge">{{ $trashedNotes->count() }}</span></h3></div></div>
        @if ($trashedNotes->isEmpty())
            <div class="empty-state trash-empty"><i class="bi bi-journal-check"></i><h4>No deleted notes</h4><p>Notes you remove will appear here temporarily.</p></div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($trashedNotes as $note)
                    <div class="col"><article class="card trash-card h-100"><div class="card-body d-flex flex-column"><div class="trash-card-top"><span class="trash-kind"><i class="bi bi-journal-text me-1"></i>Note</span><i class="bi bi-three-dots text-muted"></i></div><h4>{{ $note->title }}</h4><p class="trash-description grow">{{ $note->description ?: 'No description added.' }}</p><p class="trash-meta"><i class="bi bi-clock me-1"></i>Deleted {{ $note->deleted_at->format('M j, Y · H:i') }}</p><div class="trash-actions"><form action="{{ route('trash.notes.restore', $note->sno) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button></form><form action="{{ route('trash.notes.force-delete', $note->sno) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this item?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3 me-1"></i>Delete</button></form></div></div></article></div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="trash-section">
        <div class="section-heading"><div><span class="eyebrow">Scheduled tasks</span><h3>Deleted reminders <span class="count-badge">{{ $trashedReminders->count() }}</span></h3></div></div>
        @if ($trashedReminders->isEmpty())
            <div class="empty-state trash-empty"><i class="bi bi-calendar-check"></i><h4>No deleted reminders</h4><p>Deleted reminders will stay here until restored or cleared.</p></div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($trashedReminders as $reminder)
                    <div class="col"><article class="card trash-card h-100"><div class="card-body d-flex flex-column"><div class="trash-card-top"><span class="trash-kind"><i class="bi bi-calendar2-check me-1"></i>Reminder</span><i class="bi bi-three-dots text-muted"></i></div><h4>{{ $reminder->title }}</h4><p class="trash-description grow">{{ $reminder->description ?: 'No description added.' }}</p><p class="trash-meta mb-1"><i class="bi bi-calendar3 me-1"></i>{{ $reminder->remind_date->format('M j, Y') }} at {{ substr((string) $reminder->remind_time, 0, 5) }}</p><p class="trash-meta"><i class="bi bi-clock me-1"></i>Deleted {{ $reminder->deleted_at->format('M j, Y · H:i') }}</p><div class="trash-actions"><form action="{{ route('trash.reminders.restore', $reminder->id) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button></form><form action="{{ route('trash.reminders.force-delete', $reminder->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this item?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3 me-1"></i>Delete</button></form></div></div></article></div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection

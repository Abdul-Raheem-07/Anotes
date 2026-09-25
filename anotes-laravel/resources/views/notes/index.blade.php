@extends('layouts.app')

@section('title', 'My Notes - ANotes')

@section('content')
<div class="container my-5 page-frame">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="page-heading">
        <div><span class="eyebrow"><i class="bi bi-journal-text me-1"></i>Personal library</span><h2>My Notes</h2><p>Keep your thoughts clear, searchable in your mind, and close at hand.</p></div>
        <a href="{{ route('notes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>New Note</a>
    </div>

    @if ($notes->isEmpty())
        <div class="empty-state"><i class="bi bi-journal-plus"></i><h4>Your notebook is ready</h4><p>You have not created any notes yet. Start with a thought worth keeping.</p><a href="{{ route('notes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Create your first note</a></div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($notes as $note)
                <div class="col">
                    <div class="card note-card h-100">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="note-card-top"><span class="note-type"><i class="bi bi-sticky"></i> Note</span><span class="note-menu-dot"></span></div>
                            <h5 class="card-title fw-bold text-dark mb-2">{{ $note->title }}</h5>
                            <p class="card-text text-muted grow note-preview">{{ $note->description }}</p>
                            <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $note->tstamp }}</small>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('notes.edit', $note) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('notes.destroy', $note) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this note?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

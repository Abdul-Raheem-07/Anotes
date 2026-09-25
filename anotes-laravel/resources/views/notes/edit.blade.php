@extends('layouts.app')

@section('title', 'Edit Note - ANotes')

@section('content')
<div class="container my-5 page-frame">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card editor-card">
                <div class="card-body p-4 p-md-5">
                    <div class="editor-heading"><div><span class="eyebrow">Personal library</span><h2>Edit note</h2><p>Refine the thought and keep it moving.</p></div><a href="{{ route('notes.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a></div>
                    </div>

                    <form action="{{ route('notes.update', $note) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="editor-field">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $note->title) }}" placeholder="What is on your mind?" required autofocus>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="editor-field">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror editor-textarea" id="description" name="description" rows="9" placeholder="Write your note here...">{{ old('description', $note->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="editor-actions"><a href="{{ route('notes.index') }}" class="btn btn-ghost">Cancel</a><button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-2"></i>Update Note</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

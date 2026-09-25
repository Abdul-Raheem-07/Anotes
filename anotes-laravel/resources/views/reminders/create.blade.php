@extends('layouts.app')

@section('title', 'Add Reminder - ANotes')

@section('content')
<div class="container my-5 page-frame">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card editor-card">
                <div class="card-body p-4 p-md-5">
                    <div class="editor-heading"><div><span class="eyebrow">Personal schedule</span><h2>New reminder</h2><p>Choose when this should come back to your attention.</p></div><a href="{{ route('reminders.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a></div>

                    <div class="form-section-label"><i class="bi bi-card-text me-2"></i>Reminder details</div>
                    </div>

                    <form action="{{ route('reminders.store') }}" method="POST" class="editor-form">
                        @csrf

                        <div class="editor-field">
                            <label for="title" class="form-label fw-semibold">Title</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   placeholder="Enter reminder title"
                                   required 
                                   autofocus>
                            @error('title')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="editor-field">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Enter description (optional, max 500 characters)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-section-label mt-4"><i class="bi bi-calendar3 me-2"></i>Schedule</div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="remind_date" class="form-label fw-semibold">Remind Date</label>
                                <input type="date" 
                                       class="form-control @error('remind_date') is-invalid @enderror" 
                                       id="remind_date" 
                                       name="remind_date" 
                                       value="{{ old('remind_date', date('Y-m-d')) }}" 
                                       required>
                                @error('remind_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="remind_time" class="form-label fw-semibold">Remind Time</label>
                                <input type="time" 
                                       class="form-control @error('remind_time') is-invalid @enderror" 
                                       id="remind_time" 
                                       name="remind_time" 
                                       value="{{ old('remind_time', date('H:i')) }}" 
                                       required>
                                @error('remind_time')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="repeat_option" class="form-label fw-semibold">Repeat Option</label>
                                <select class="form-select @error('repeat_option') is-invalid @enderror" 
                                        id="repeat_option" 
                                        name="repeat_option"
                                        onchange="toggleCustomDays()">
                                    <option value="none" {{ old('repeat_option', 'none') === 'none' ? 'selected' : '' }}>None</option>
                                    <option value="daily" {{ old('repeat_option') === 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('repeat_option') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('repeat_option') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="custom" {{ old('repeat_option') === 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                                @error('repeat_option')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 {{ old('repeat_option') === 'custom' ? '' : 'd-none' }}" id="custom_days_container">
                                <label for="custom_days" class="form-label fw-semibold">Custom Days (1-7)</label>
                                <input type="number" 
                                       class="form-control @error('custom_days') is-invalid @enderror" 
                                       id="custom_days" 
                                       name="custom_days" 
                                       min="1" 
                                       max="7" 
                                       value="{{ old('custom_days', 1) }}"
                                       placeholder="Number of days">
                                @error('custom_days')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="editor-actions"><a href="{{ route('reminders.index') }}" class="btn btn-ghost">Cancel</a><button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-2"></i>Save Reminder</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCustomDays() {
        const repeatOption = document.getElementById('repeat_option').value;
        const customDaysContainer = document.getElementById('custom_days_container');
        if (repeatOption === 'custom') {
            customDaysContainer.style.display = 'block';
        } else {
            customDaysContainer.style.display = 'none';
        }
    }
</script>
@endsection

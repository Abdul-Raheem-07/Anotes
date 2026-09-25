<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    /**
     * Display a listing of the user's active reminders.
     */
    public function index()
    {
        $reminders = Reminder::where('user_id', Auth::id())
            ->orderBy('remind_date', 'asc')
            ->orderBy('remind_time', 'asc')
            ->get();

        $remindersData = $reminders->map(function ($r) {
            return [
                'id' => (int) $r->id,
                'title' => $r->title,
                'description' => $r->description,
                'remind_date' => is_object($r->remind_date) ? $r->remind_date->format('Y-m-d') : (string) $r->remind_date,
                'remind_time' => (string) $r->remind_time,
                'repeat_option' => $r->repeat_option ?? 'none',
                'custom_days' => $r->custom_days ? (int) $r->custom_days : null,
                'is_done' => (bool) $r->is_done,
            ];
        });

        return view('reminders.index', compact('reminders', 'remindersData'));
    }

    /**
     * Show the form for creating a new reminder.
     */
    public function create()
    {
        return view('reminders.create');
    }

    /**
     * Store a newly created reminder in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'remind_date' => 'required|date',
            'remind_time' => 'required',
            'repeat_option' => 'nullable|in:none,daily,weekly,monthly,custom',
            'custom_days' => 'nullable|integer|min:1|max:7',
        ]);

        $reminder = new Reminder();
        $reminder->user_id = Auth::id();
        $reminder->title = $validated['title'];
        $reminder->description = $validated['description'] ?? null;
        $reminder->remind_date = $validated['remind_date'];
        $reminder->remind_time = $validated['remind_time'];
        $reminder->repeat_option = $validated['repeat_option'] ?? 'none';
        $reminder->custom_days = ($validated['repeat_option'] ?? 'none') === 'custom' ? ($validated['custom_days'] ?? null) : null;
        $reminder->is_done = false;
        $reminder->save();

        return redirect()->route('reminders.index')->with('success', 'Reminder created successfully.');
    }

    /**
     * Show the form for editing the specified reminder.
     */
    public function edit(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('reminders.edit', compact('reminder'));
    }

    /**
     * Update the specified reminder in storage.
     */
    public function update(Request $request, Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'remind_date' => 'required|date',
            'remind_time' => 'required',
            'repeat_option' => 'nullable|in:none,daily,weekly,monthly,custom',
            'custom_days' => 'nullable|integer|min:1|max:7',
        ]);

        $reminder->title = $validated['title'];
        $reminder->description = $validated['description'] ?? null;
        $reminder->remind_date = $validated['remind_date'];
        $reminder->remind_time = $validated['remind_time'];
        $reminder->repeat_option = $validated['repeat_option'] ?? 'none';
        $reminder->custom_days = ($validated['repeat_option'] ?? 'none') === 'custom' ? ($validated['custom_days'] ?? null) : null;
        $reminder->save();

        return redirect()->route('reminders.index')->with('success', 'Reminder updated successfully.');
    }

    /**
     * Toggle the completion status of the specified reminder.
     */
    public function toggleDone(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $reminder->is_done = !$reminder->is_done;
        $reminder->save();

        $statusMsg = $reminder->is_done ? 'Reminder marked as completed.' : 'Reminder marked as pending.';

        return redirect()->back()->with('success', $statusMsg);
    }

    /**
     * Remove the specified reminder from storage (Soft Delete).
     */
    public function destroy(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $reminder->delete();

        return redirect()->route('reminders.index')->with('success', 'Reminder deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    /**
     * Display all soft-deleted Notes and Reminders belonging to the authenticated user.
     */
    public function index()
    {
        $trashedNotes = Note::onlyTrashed()
            ->where('user_id', Auth::id())
            ->orderBy('deleted_at', 'desc')
            ->get();

        $trashedReminders = Reminder::onlyTrashed()
            ->where('user_id', Auth::id())
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('trash.index', compact('trashedNotes', 'trashedReminders'));
    }

    /**
     * Restore a soft-deleted Note.
     */
    public function restoreNote(int $id)
    {
        $note = Note::onlyTrashed()->where('sno', $id)->firstOrFail();

        if ((int) $note->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $note->restore();

        return redirect()->route('trash.index')->with('success', 'Note restored successfully. It is back in your Notes.');
    }

    /**
     * Permanently delete a soft-deleted Note.
     */
    public function forceDeleteNote(int $id)
    {
        $note = Note::onlyTrashed()->where('sno', $id)->firstOrFail();

        if ((int) $note->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $note->forceDelete();

        return redirect()->route('trash.index')->with('success', 'Note permanently deleted.');
    }

    /**
     * Restore a soft-deleted Reminder.
     */
    public function restoreReminder(int $id)
    {
        $reminder = Reminder::onlyTrashed()->where('id', $id)->firstOrFail();

        if ((int) $reminder->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $reminder->restore();

        return redirect()->route('trash.index')->with('success', 'Reminder restored successfully. It is back in your Reminders.');
    }

    /**
     * Permanently delete a soft-deleted Reminder.
     */
    public function forceDeleteReminder(int $id)
    {
        $reminder = Reminder::onlyTrashed()->where('id', $id)->firstOrFail();

        if ((int) $reminder->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $reminder->forceDelete();

        return redirect()->route('trash.index')->with('success', 'Reminder permanently deleted.');
    }
}

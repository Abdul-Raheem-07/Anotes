<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the user's active notes.
     */
    public function index()
    {
        $notes = Note::where('user_id', Auth::id())
            ->orderBy('tstamp', 'desc')
            ->get();

        return view('notes.index', compact('notes'));
    }

    /**
     * Show the form for creating a new note.
     */
    public function create()
    {
        return view('notes.create');
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $note = new Note();
        $note->user_id = Auth::id();
        $note->title = $validated['title'];
        $note->description = $validated['description'] ?? null;
        $note->save();

        return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    /**
     * Show the form for editing the specified note.
     */
    public function edit(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('notes.edit', compact('note'));
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $note->title = $validated['title'];
        $note->description = $validated['description'] ?? null;
        $note->save();

        return redirect()->route('notes.index')->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified note from storage (Soft Delete).
     */
    public function destroy(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->journals()->orderBy('entry_date', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('content', 'like', $searchTerm);
            });
        }

        if ($request->filled('start_date')) {
            $query->where('entry_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('entry_date', '<=', $request->end_date);
        }

        $journals = $query->paginate(7)->withQueryString();
        $view = $request->get('view', 'list');
        
        $selectedJournal = null;
        if ($request->has('id')) {
            $selectedJournal = $journals->getCollection()->firstWhere('id', $request->id);
            // Fallback: If requested journal is not on the current page, fetch it directly
            if (!$selectedJournal && Auth::id()) {
                $found = Journal::find($request->id);
                if ($found && $found->user_id === Auth::id()) {
                    $selectedJournal = $found;
                }
            }
        } elseif ($journals->total() > 0) {
            $selectedJournal = $journals->first();
        }

        return view('journals.index', compact('journals', 'view', 'selectedJournal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'entry_date' => [
                'required',
                'date',
                \Illuminate\Validation\Rule::unique('journals')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'rating' => 'integer|min:1|max:5',
            'image' => 'nullable|image|max:5120',
        ], [
            'entry_date.unique' => 'You have already created a journal entry for this date.',
        ]);

        $journal = new Journal();
        $journal->user_id = Auth::id();
        $journal->title = $request->title;
        $journal->content = $request->content;
        $journal->entry_date = $request->entry_date;
        $journal->rating = $request->rating ?? 3;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('journals', 'public');
            $journal->image = $path;
        }

        $journal->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Journal saved successfully!',
                'journal' => $journal
            ]);
        }

        return redirect()->route('journals.index')->with('success', 'Journal saved successfully!');
    }

    public function edit(Journal $journal)
    {
        if ($journal->user_id !== Auth::id()) abort(403);
        
        // Use the index layout but render edit view
        return redirect()->route('journals.index', ['view' => 'edit', 'id' => $journal->id]);
    }

    public function update(Request $request, Journal $journal)
    {
        if ($journal->user_id !== Auth::id()) abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'entry_date' => [
                'required',
                'date',
                \Illuminate\Validation\Rule::unique('journals')->ignore($journal->id)->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'rating' => 'integer|min:1|max:5',
            'image' => 'nullable|image|max:5120',
        ], [
            'entry_date.unique' => 'You have already created a journal entry for this date.',
        ]);

        $journal->title = $request->title;
        $journal->content = $request->content;
        $journal->entry_date = $request->entry_date;
        $journal->rating = $request->rating ?? 3;

        if ($request->hasFile('image')) {
            if ($journal->image) {
                Storage::disk('public')->delete($journal->image);
            }
            $path = $request->file('image')->store('journals', 'public');
            $journal->image = $path;
        }

        $journal->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Journal updated successfully!',
                'journal' => $journal
            ]);
        }

        return redirect()->route('journals.index', ['id' => $journal->id])->with('success', 'Journal updated successfully!');
    }

    public function destroy(Request $request, Journal $journal)
    {
        if ($journal->user_id !== Auth::id()) abort(403);

        if ($journal->image) {
            Storage::disk('public')->delete($journal->image);
        }
        $journal->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Journal deleted successfully!'
            ]);
        }

        return redirect()->route('journals.index')->with('success', 'Journal deleted successfully!');
    }
}

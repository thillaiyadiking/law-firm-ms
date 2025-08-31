<?php
// app/Http/Controllers/CaseNoteController.php
namespace App\Http\Controllers;

use App\Models\CaseModel;
use App\Models\CaseNote;
use Illuminate\Http\Request;

class CaseNoteController extends Controller
{
    public function store(Request $request, CaseModel $case)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $note = CaseNote::create([
            'case_id' => $case->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        $note->load('user');

        return back()->with('success', 'Note added successfully.');
    }

    public function destroy(CaseNote $note)
    {
        $note->delete();
        return back()->with('success', 'Note deleted successfully.');
    }
}

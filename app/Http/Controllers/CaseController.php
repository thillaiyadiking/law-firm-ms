<?php
// app/Http/Controllers/CaseController.php
namespace App\Http\Controllers;

use App\Models\CaseModel;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseModel::with(['assignedTo', 'createdBy'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('case_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            });

        $cases = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Cases/Index', [
            'cases' => $cases,
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'name')->get();

        return Inertia::render('Cases/Create', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:open,in_progress,closed,pending',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        $case = CaseModel::create([
            ...$request->all(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('cases.show', $case)
            ->with('success', 'Case created successfully.');
    }

    public function show(CaseModel $caseModel)
    {
        $caseModel->load([
            'assignedTo',
            'createdBy',
            'notes.user',
            'files.uploadedBy'
        ]);
        // return $caseModel;
        return Inertia::render('Cases/Show', [
            'caseItemProp' => $caseModel,
            'users' => User::select('id', 'name')->get(),
        ]);
    }

    public function edit(CaseModel $case)
    {
        $users = User::select('id', 'name')->get();

        return Inertia::render('Cases/Edit', [
            'case' => $case,
            'users' => $users,
        ]);
    }

    public function update(Request $request, CaseModel $case)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:open,in_progress,closed,pending',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $case->update($request->all());

        return redirect()->route('cases.show', $case)
            ->with('success', 'Case updated successfully.');
    }

    public function destroy(CaseModel $case)
    {
        $case->delete();

        return redirect()->route('cases.index')
            ->with('success', 'Case deleted successfully.');
    }
}

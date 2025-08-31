<?php
// app/Http/Controllers/CaseFileController.php
namespace App\Http\Controllers;

use App\Models\CaseModel;
use App\Models\CaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CaseFileController extends Controller
{
    public function store(Request $request, CaseModel $case)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB max
        ]);

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('case-files', $filename, 'public');

            $caseFile = CaseFile::create([
                'case_id' => $case->id,
                'uploaded_by' => auth()->id(),
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path,
            ]);

            $uploadedFiles[] = $caseFile;
        }

        return back()->with('success', count($uploadedFiles) . ' file(s) uploaded successfully.');
    }

    public function download(CaseFile $file)
    {
        return Storage::disk('public')->download($file->path, $file->original_name);
    }

    public function destroy(CaseFile $file)
    {
        Storage::disk('public')->delete($file->path);
        $file->delete();

        return back()->with('success', 'File deleted successfully.');
    }
}

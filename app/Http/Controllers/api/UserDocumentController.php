<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Storage;

class UserDocumentController extends Controller
{
    // List all documents for the authenticated user
    public function index()
    {
        $documents = UserDocument::where('user_id', auth()->id())->get();
        return response()->json($documents);
    }

    // Store a new document for the authenticated user
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
        ]);

        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $path = $file->store('user_documents', 'public');

        $document = UserDocument::create([
            'user_id' => auth()->id(),
            'title' => pathinfo($originalName, PATHINFO_FILENAME),
            'file_path' => $path,
            'type' => $extension,
        ]);

        return response()->json($document, 201);
    }

    // Show a specific document (only if it belongs to the user)
    public function show($id)
    {
        $document = UserDocument::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json($document);
    }

    // Delete a document (only if it belongs to the user)
    public function destroy($id)
    {
        $document = UserDocument::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['message' => 'Document deleted successfully.']);
    }
}

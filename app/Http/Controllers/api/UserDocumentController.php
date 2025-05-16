<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

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








    // Admin: List all documents for a specific user
public function adminIndex($user_id)
{
    $this->authorizeAdmin(); // Optional: if you want to restrict to admin users
    $documents = UserDocument::where('user_id', $user_id)->get();
    return response()->json($documents);
}

// Admin: Upload document for a specific user
public function adminStore(Request $request)
{
    $this->authorizeAdmin(); // Optional

    $request->validate([
        'file' => 'required|file|max:20480', // 20MB
        'user_id' => 'required|exists:users,id',
    ]);

    $file = $request->file('file');

    $originalName = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();
    $path = $file->store('user_documents', 'public');

    $document = UserDocument::create([
        'user_id' => $request->user_id,
        'title' => pathinfo($originalName, PATHINFO_FILENAME),
        'file_path' => $path,
        'type' => $extension,
    ]);

    return response()->json($document, 201);
}

// Admin: Show a specific document
public function adminShow($id)
{
    $this->authorizeAdmin(); // Optional

    $document = UserDocument::findOrFail($id);
    return response()->json($document);
}

// Admin: Delete a specific document
public function adminDestroy($id)
{
    $this->authorizeAdmin(); // Optional

    $document = UserDocument::findOrFail($id);
    Storage::disk('public')->delete($document->file_path);
    $document->delete();

    return response()->json(['message' => 'Document deleted successfully.']);
}

// Optional: Helper to restrict admin access
protected function authorizeAdmin()
{
    if (!auth()->user() || auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized.');
    }
}







}

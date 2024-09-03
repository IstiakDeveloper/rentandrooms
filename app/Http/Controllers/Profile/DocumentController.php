<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\BankDetails;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_1' => 'required|string',
            'path_1' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'type_2' => 'required|string',
            'path_2' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'type_3' => 'nullable|string',
            'path_3' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'type_4' => 'nullable|string',
            'path_4' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ]);
    
        // Handle file uploads and store paths
        $paths = [];
        for ($i = 1; $i <= 4; $i++) {
            if ($request->hasFile("path_{$i}")) {
                $paths["path_{$i}"] = $request->file("path_{$i}")->store('documents', 'public');
            }
        }
    
        // Find the existing document or create a new one
        $document = Document::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'type_1' => $validated['type_1'],
                'path_1' => $paths['path_1'] ?? null,
                'type_2' => $validated['type_2'],
                'path_2' => $paths['path_2'] ?? null,
                'type_3' => $validated['type_3'] ?? null,
                'path_3' => $paths['path_3'] ?? null,
                'type_4' => $validated['type_4'] ?? null,
                'path_4' => $paths['path_4'] ?? null,
            ]
        );    
        return redirect()->back()->with('success', 'Documents saved successfully.');
    }
    

    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first(); // Assuming a user has one role

        $documents = Document::where('user_id', $user->id)->get();
        $bankDetail = BankDetails::where('user_id', $user->id)->first();
        
        return Inertia::render('Documents/UploadDocuments', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
            ],
            'documents' => $documents,
            'bankDetail' => $bankDetail,
        ]);
    }

    public function storeBankDetails(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'sort_code' => 'required|string|max:11',
        ]);

        $user = Auth::user();
        
        // Store or update bank details
        $bankDetail = BankDetails::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->back()->with('success', 'Bank details saved successfully.');
    }


    public function download($id, $type)
    {
        $document = Document::findOrFail($id);
        $filePath = $document->{'path_' . $type};

        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }

        return response()->json(['message' => 'File not found'], 404);
    }



}

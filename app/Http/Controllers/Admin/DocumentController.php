<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ShipmentFile;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['shipmentFile.client', 'documentType', 'uploadedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('shipmentFile', fn($fq) => $fq->where('file_no', 'like', "%{$search}%"))
                  ->orWhereHas('documentType', fn($dq) => $dq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('file_id')) {
            $query->where('shipment_file_id', $request->file_id);
        }

        $documents = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.documents.partials.table', compact('documents'))->render(),
            ]);
        }

        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        $files = ShipmentFile::with('client')->latest()->limit(50)->get();
        $documentTypes = DocumentType::orderBy('name')->get();
        return view('admin.documents.create', compact('files', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_file_id' => 'required|exists:shipment_files,id',
            'document_type_id' => 'required|exists:document_types,id',
            'reference_no' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'original_received' => 'boolean',
        ]);

        $path = $request->file('file')->store('documents', 'private');

        $document = Document::create([
            'shipment_file_id' => $validated['shipment_file_id'],
            'document_type_id' => $validated['document_type_id'],
            'reference_no' => $validated['reference_no'] ?? null,
            'file_path' => $path,
            'original_received' => $validated['original_received'] ?? false,
            'received_at' => ($validated['original_received'] ?? false) ? now() : null,
            'uploaded_by' => auth()->id(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Document uploaded', 'document' => $document]);
        }

        return redirect()->route('admin.documents.index')->with('status', 'Document uploaded');
    }

    public function show(Document $document)
    {
        $document->load(['shipmentFile.client', 'documentType', 'uploadedBy', 'verifiedBy']);
        return view('admin.documents.show', compact('document'));
    }

    public function verify(Request $request, Document $document)
    {
        if ($document->verified_at) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Already verified'], 400);
            }
            return back()->with('error', 'Already verified');
        }

        $document->update([
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Document verified']);
        }

        return back()->with('status', 'Document verified');
    }

    public function markOriginalReceived(Request $request, Document $document)
    {
        $document->update([
            'original_received' => true,
            'received_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Original marked as received']);
        }

        return back()->with('status', 'Original received');
    }

    public function download(Document $document)
    {
        return Storage::disk('private')->download($document->file_path);
    }

    public function destroy(Request $request, Document $document)
    {
        Storage::disk('private')->delete($document->file_path);
        $document->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Document deleted']);
        }

        return redirect()->route('admin.documents.index')->with('status', 'Document deleted');
    }
}

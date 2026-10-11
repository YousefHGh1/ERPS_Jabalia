<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index documents', ['only' => ['index']]);
        $this->middleware('permission:store documents', ['only' => ['store']]);
        $this->middleware('permission:update documents', ['only' => ['update']]);
        $this->middleware('permission:delete documents', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $documentType = $request->input('document_type');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $documents = Document::with(['creator'])
            ->when($search, function ($query) use ($search) {
                $query->where('document_number', 'like', '%' . $search . '%')
                      ->orWhere('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->when($documentType, function ($query) use ($documentType) {
                $query->where('document_type', $documentType);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('document_date', [$fromDate, $toDate]);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(StoreDocumentRequest $request)
    {
        try {
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('documents', 'public');
                $file = $request->file('file');
                
                $documentData = [
                    'document_number' => $request->document_number,
                    'document_type' => $request->document_type,
                    'document_date' => $request->document_date,
                    'title' => $request->title,
                    'description' => $request->description,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'status' => 'active',
                    'created_by' => Auth::id(),
                ];

                Document::create($documentData);

                return redirect()->route('documents.index')
                    ->with('success', 'تم إضافة المستند بنجاح');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'يرجى اختيار ملف');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المستند: ' . $e->getMessage());
        }
    }

    public function edit(Document $document)
    {
        return view('documents.edit', compact('document'));
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        try {
            $documentData = [
                'document_number' => $request->document_number,
                'document_type' => $request->document_type,
                'document_date' => $request->document_date,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ];

            // Handle file upload
            if ($request->hasFile('file')) {
                // Delete old file
                if ($document->file_path) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $path = $request->file('file')->store('documents', 'public');
                $file = $request->file('file');
                
                $documentData['file_path'] = $path;
                $documentData['file_name'] = $file->getClientOriginalName();
                $documentData['file_size'] = $file->getSize();
                $documentData['mime_type'] = $file->getMimeType();
            }

            $document->update($documentData);

            return redirect()->route('documents.index')
                ->with('success', 'تم تحديث المستند بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المستند: ' . $e->getMessage());
        }
    }

    public function destroy(Document $document)
    {
        try {
            // Delete file from storage
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $document->delete();
            
            return redirect()->route('documents.index')
                ->with('success', 'تم حذف المستند بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('documents.index')
                ->with('error', 'حدث خطأ أثناء حذف المستند: ' . $e->getMessage());
        }
    }

    public function download(Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (file_exists($filePath)) {
            return response()->download($filePath, $document->file_name);
        }
        
        return back()->with('error', 'الملف غير موجود');
    }
}

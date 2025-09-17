<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Models\Paybill;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{


    public function create()
    {
       
        return view('uploads.create');
    }
    public function index()
    {
        $uploads = Upload::where('user_id', auth()->id())->latest()->get();
        return view('uploads.index', compact('uploads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename);

        $upload = Upload::create([
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'rows_count' => 0,
            'status' => 'uploaded',
        ]);

        // Parse Excel in background
        Excel::import(new ContactsImport($upload), $path);

        return redirect()->route('uploads.index')->with('success', 'File uploaded successfully.');
    }

    public function contacts(Upload $upload)
    {
        // $this->authorize('view', $upload);
        
        $contacts = $upload->contacts()->paginate(50);
        return view('uploads.contacts', compact('upload', 'contacts'));
    }

    public function show(Upload $upload)
    {
        // $this->authorize('view', $upload);
        $paybills = Paybill::where('user_id', auth()->id())->get();
        
        $contacts = $upload->contacts()->paginate(50);
        return view('uploads.show', compact('upload', 'contacts','paybills'));
    }
}
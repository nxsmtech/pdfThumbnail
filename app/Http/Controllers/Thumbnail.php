<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class Thumbnail extends Controller
{
    public function index()
    {
        return view('thumbnails');
    }

    public function getDocument($id)
    {
//        $document = Document::findOrFail($id);
//
//        $filePath = $document->file_path;
//
//        // file not found
//        if( ! Storage::exists($filePath) ) {
//            abort(404);
//        }

//        $pdfContent = Storage::get($filePath);


        $fileName = 'document.pdf';
        $path = storage_path('app\public\pdf\document.pdf');

//        // for pdf, it will be 'application/pdf'
//        $type       = Storage::mimeType($path);
//        $fileName   = Storage::name($path);

        return Response::make(file_get_contents($path), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"'
        ]);
    }
    
    public function addNew(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'select_file' =>
                'required|mimes:pdf|max:2048'
        ]);

        if ($validation->passes()) {
            try {
                $file = $request->file('select_file');
                $new_name = str_replace(' ', '', $file->getClientOriginalName());
                $file->move(public_path('files/pdf'), $new_name);

                $imgName = explode(".", $new_name);
                $command = 'convert files/pdf/' . $new_name . '[0]' . ' files/thumbnails/' . $imgName[0] . '.jpg';
                exec($command);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage()
                ]);
            }

            return response()->json([
                'message' => 'Uploaded successfully',
                'uploaded_file' => '<img src="/files/thumbnails/' . $imgName[0] . '.jpg' . '" class="img-thumbnail" width="300" />',
                'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                'message' => $validation->errors()->all(),
                'uploaded_file' => '',
                'class_name' => 'alert-danger'
            ]);
        }
    }

}
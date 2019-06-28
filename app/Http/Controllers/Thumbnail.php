<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class Thumbnail extends Controller
{
    public function index()
    {
        $path = public_path('files\thumbnails');
        $thumbnails = array();
        if (file_exists($path)) {
            $thumbnails = File::allFiles($path);
        }
        return view('thumbnails')->with('thumbnails', $thumbnails);
    }

    public function getDocument($name)
    {
        $path = public_path('files/pdf') . $name;
        $file = File::get($path);
    }
    
    public function addNew(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'select_file' =>
                'required|mimes:pdf|max:4096'
        ]);

        if ($validation->passes()) {
            try {
                $file = $request->file('select_file');
                $new_name = str_replace(' ', '', rand() .  $file->getClientOriginalName());
                $file->move(public_path('files/pdf'), $new_name);

                $imgName = explode(".", $new_name);
                $command = 'convert files/pdf/' . $new_name . '[0]' . ' files/thumbnails/' . rand() . $imgName[0] . '.jpg';
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
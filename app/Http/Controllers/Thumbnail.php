<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Imagick;

class Thumbnail extends Controller
{
    public function index()
    {
        return view('thumbnails');
    }

    public function addNew(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'select_file' =>
                'required'
        ]);

        if ($validation->passes()) {
            $file = $request->file('select_file');
            $new_name = $file->getClientOriginalName();
            $file->move(public_path('files'), $new_name);
            try {
                $im = new Imagick();
                $im->readImage(public_path('files') . '\06062019-164145.pdf');
                $im->destroy();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage()
                ]);
            }

            return response()->json([
                'message' => 'Uploaded successfully',
                'uploaded_file' => '<img src="/files/>' . $new_name . '" class="img-thumbnail" width="300" />',
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
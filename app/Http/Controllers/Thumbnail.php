<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class Thumbnail extends Controller
{
    public function index()
    {
        $path = public_path('files\thumbnails');
        $paginatedSearchResults = array();
        $paginator = null;
        if (file_exists($path)) {
            $thumbnails = File::allFiles($path);
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $collection = new Collection($thumbnails);
            $perPage = 20;
            $currentPageSearchResults = $collection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginatedSearchResults= new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
        }
        return view('main',['thumbnails' => $paginatedSearchResults]);
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
                $randomID = rand();
                $new_name = str_replace(' ', '', $randomID .  $file->getClientOriginalName());
                $file->move(public_path('files/pdf'), $new_name);

                $imgName = substr($new_name, 0, -4);
                $command = 'convert files/pdf/' . $new_name . '[0]' . ' files/thumbnails/' . $imgName . '.jpg';
                exec($command);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage()
                ]);
            }

            return response()->json([
                'message' => 'Uploaded successfully',
                'uploaded_file_name' => $imgName,
                'uploaded_file' => asset('files/pdf') . '/' . $new_name,
                'uploaded_thumbnail' => asset('/files/thumbnails') . '/' . $imgName . '.jpg',
                'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                'message' => $validation->errors()->all(),
                'uploaded_file_name' => '',
                'uploaded_file' => '',
                'uploaded_thumbnail' => '',
                'class_name' => 'alert-danger'
            ]);
        }
    }

    public function delete(Request $request)
    {
        $deleteName = $request->get('delete_name');
        if ($deleteName) {
            $filePath = public_path('files/pdf') . '/' . $deleteName . '.pdf';
            $thumbnailPath = public_path('files/thumbnails') . '/' . $deleteName . '.jpg';
            try {
                File::delete($filePath);
                File::delete($thumbnailPath);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage()
                ]);
            }

            return response()->json([
                'message' => 'Delete successfully',
                'class_name' => 'alert-success'
            ]);
        } else {
            return response()->json([
                'message' => 'File name was not passed',
                'class_name' => 'alert-danger'
            ]);
        }


    }
}
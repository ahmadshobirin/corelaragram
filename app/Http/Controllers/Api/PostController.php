<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use File;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;


class PostController extends Controller
{
    private $path = 'uploads/posts';

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png',
            'caption' => 'string',
        ]);

        // dd(File::exists($this->path));
        DB::beginTransaction();

        try {

            // if(!File::exists($this->path)) //check directory exist or make
            //     File::makeDirectory($this->path, 0777, true, true);
            $file = $request->file('file');
            $newName  = time().'.'.$file->getClientOriginalExtension();
            $newImage = Image::make($file)
                        ->resize(1920,1080)
                        ->save($this->path."/".$newName);

            $request->merge([
                'user_id' => Auth::user()->id,
                'image' => "posts/$newName",
            ]);

            Post::create($request->only([
                'user_id','image','caption'
            ]));

            DB::commit();
        } catch (\Throwable $th) {
            return $th;
        }catch (\Exception $e) {
            return $e;
        }

        return response()->json([
            'status'      => true,
            'description' => 'post succes'
        ]);
    }
}

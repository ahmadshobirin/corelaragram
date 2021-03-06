<?php

namespace App\Http\Controllers\Api;

use DB;
use Auth;
use File;
use JWTAuth;
use Response;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Intervention\Image\Facades\Image;


class PostController extends Controller
{
    protected $path = 'uploads/posts';

    public function index(Request $request)
    {
        $posts = Post::where('user_id',Auth::user()->id)
                ->with('likes','comments')
                ->get();

        return PostResource::collection($posts)
            ->additional([
                'status' => [
                    'code'        => 200,
                    'description' => 'OK'
                ]
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png',
            'caption' => 'string',
        ]);

        DB::beginTransaction();

        try {

            $file = $request->file('file');
            $newName  = time() . '.' . $file->getClientOriginalExtension();

            Image::make($file)
                ->resize(1920, 1080)
                ->save($this->path . "/" . $newName);

            $request->merge([
                'user_id' => Auth::user()->id,
                'image' => "posts/$newName",
            ]);

            $post = Post::create($request->only(['user_id', 'image', 'caption']));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }

        return (new PostResource($post))->additional([
            'status' => [
                'code'        => 200,
                'description' => 'Post Created'
            ]
        ])->response()->setStatusCode(200);
    }

    public function displayImage($endfolder,$filename)
    {
        $path = public_path("uploads/$endfolder/$filename");

        if (!File::exists($path)) abort(404);

        $response = Response::make(File::get($path), 200);

        $response->header("Content-Type", File::mimeType($path));

        return $response;
    }

    public function like(Request $req)
    {
        $likeUser = Like::where('user_id',Auth::user()->id)
                        ->where('post_id',$req->post_id)
                        ->count();
        if($likeUser <= 0){
            Like::create([
                'user_id' => Auth::user()->id,
                'post_id' => $req->post_id,
            ]);

            return response()->json([
                'data' => [
                    'status'      => true,
                    'description' => 'user was liked post'
                ]
            ]);
        }else{

            Like::where('user_id',Auth::user()->id)
                        ->where('post_id',$req->post_id)
                        ->delete();

            return response()->json([
                'data' => [
                    'status'      => true,
                    'description' => 'user was unlike post'
                ]
            ]);
        }
    }

    public function comment(Request $req)
    {
        Comment::create([
            'user_id' => Auth::user()->id,
            'post_id' => $req->post_id,
            'comment' => $req->comment,
        ]);

        return response()->json([
            'data' => [
                'status'      => true,
                'description' => 'user has commented this post'
            ]
        ]);
    }
}

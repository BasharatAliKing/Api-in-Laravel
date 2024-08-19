<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();
        return response()->json([
            'status' => true,
            'mesage' => 'All Posts Data ! ',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg,gif',
            ]
        );
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation POst',
                'errors' => $validateUser->errors()->all(),
            ], 401);
        }
        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imageName = time() . "." . $ext;
        $img->move(public_path() . '/uploads', $imageName);

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Post Created Successfully ! ',
            'post' => $post,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select('id', 'title', 'description', 'image')->where('id', $id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Your Single Post',
            'data' => $data,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg,gif',
            ]
        );
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation POst',
                'errors' => $validateUser->errors()->all(),
            ], 401);
        }
        //for img code
        $postImage = Post::where('id', $id)->select('id', 'image')->get();
        // return $postImage[0]['image'];
        if ($request->image != '') {
            $path = public_path() . '/uploads/';
            //  return $path;
            if ($postImage[0]['image'] != '' && $postImage[0] != null) {
                $old_file = $path . $postImage[0]['image'];
                //  return $old_file;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $imageName = time() . "." . $ext;
            $img->move(public_path() . '/uploads', $imageName);
            //return $imageName;
        } else {
            $imageName = $postImage->image;
        }
        $post = Post::where(['id' => $id])->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Post Updated Successfully !',
            'post' => $post,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagePath = Post::select('image')->where('id', $id)->get();
        $post = Post::where('id', $id)->delete();
        // return $post[0]['image'];
        $filePath = public_path() . '/uploads/' . $imagePath[0]['image'];
        unlink($filePath);
        return response()->json([
            'status' => true,
            'message' => 'Post Deleted Successfully ! ',
            'post' => $post,
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //show all posts with Eloquent all()
//        $posts =  Post::all();

        //the same thing using DB query, in order to use this syntax need to add
        //use DB; to the top of file
//        $posts =  DB::select('SELECT * FROM posts');show all posts with Eloquent all()

        //get post by title with Eloquent where()
//        $post = Post::where('title', 'Post Two')->get();


//        $posts = Post::orderBy('title', 'desc')->get();

        //fetching just one post with take(1)
//        $posts = Post::orderBy('title', 'desc')->take(1)->get();

        //adding pagination with paginate(n) where n is the number of posts to be shown on a page
        $posts = Post::orderBy('created_at', 'desc')->paginate(1);
        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // file has to be image type, eg. jpeg, gif etc.
        //nullable which means optional so that user doesn't have to add image
        // and we set max size to 1999
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:1999'
        ]);

        // Handle file upload
        if ($request->hasFile('cover_image')){
            // Get file name with the extension
            $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();

            // Get just file name
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

            // Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();

            // File name to store
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;

            // Upload image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.jpg';
        }

        // Create Post
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id;
        $post->cover_image = $fileNameToStore;
        $post->save();

        return redirect('/posts')->with('success', 'Post Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post =  Post::find($id);
        return view('posts.show')->with('post', $post);
    }


    /**
     * Display posts by user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showPostsByUser($id)
    {
        $posts =  Post::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('posts.posts_by_user')->with('posts', $posts);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post =  Post::find($id);

        // Check if user is authorized to edit
        if (auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        return view('posts.edit')->with('post', $post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required'
        ]);

        $post = Post::find($id);

        // Handle file upload
        if ($request->hasFile('cover_image')){
            // Get file name with the extension
            $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();

            // Get just file name
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

            // Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();

            // File name to store
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;

            // Delete old image if there was one
            if ($post->cover_image !== 'noimage.jpg'){
                // Delete image
                Storage::delete('public/cover_images/' . $post->cover_image);
            }

            // Upload new image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }

        // Update Post
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        if ($request->hasFile('cover_image')){
            $post->cover_image = $fileNameToStore;
        }
        $post->save();

        return redirect('/posts')->with('success', 'Post Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        // Check if user is authorized to edit
        if (auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        if ($post->cover_image !== 'noimage.jpg'){
            // Delete image
            Storage::delete('public/cover_images/' . $post->cover_image);
        }

        // Delete post comments using hasMany relation in Post model comment() method
        $post->comment()->delete();
        $post->delete();

        return redirect('/posts')->with('success', 'Posts Removed');
    }
}

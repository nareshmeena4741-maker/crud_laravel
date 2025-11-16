<?php
public function up()
{
    Schema::create('videos', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('video_url');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}



public function up()
{
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->text('comment');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // Polymorphic fields
        $table->morphs('commentable');
        // Creates: commentable_id & commentable_type

        $table->timestamps();
    });
}


public function up()
{
    Schema::create('images', function (Blueprint $table) {
        $table->id();
        $table->string('image_path');

        // Polymorphic fields
        $table->morphs('imageable');
        // Creates: imageable_id & imageable_type

        $table->timestamps();
    });
}


class Video extends Model
{
    protected $fillable = ['title', 'description', 'video_url', 'user_id'];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}


class Comment extends Model
{
    protected $fillable = ['comment', 'user_id'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}




class Image extends Model
{
    protected $fillable = ['image_path'];

    public function imageable()
    {
        return $this->morphTo();
    }
}

$video = Video::with(['images', 'comments.images'])->find($id);


 public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'video_url'   => 'required|string',
            'user_id'     => 'required|exists:users,id',
            'images.*'    => 'nullable|image|max:2048'
        ]);

        DB::beginTransaction();

        try {
            // 1. Create Video
            $video = Video::create([
                'title'       => $request->title,
                'description' => $request->description,
                'video_url'   => $request->video_url,
                'user_id'     => $request->user_id,
            ]);

            // 2. Store Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $path = $img->store('videos', 'public');

                    $video->images()->create([
                        'image_path' => $path
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Video stored successfully',
                'data'    => $video->load('images', 'comments'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



      public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|string',
            'description' => 'nullable|string',
            'video_url'   => 'sometimes|string',
            'images.*'    => 'nullable|image|max:2048'
        ]);

        DB::beginTransaction();

        try {
            // Update basic fields
            $video->update($request->only(['title', 'description', 'video_url']));

            // If new images provided: Delete old & upload new
            if ($request->hasFile('images')) {

                // Delete old images
                foreach ($video->images as $img) {
                    \Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }

                // Upload new images
                foreach ($request->file('images') as $img) {
                    $path = $img->store('videos', 'public');

                    $video->images()->create([
                        'image_path' => $path
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Video updated successfully',
                'data'    => $video->load('images', 'comments'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


     public function destroy($id)
    {
        $video = Video::with('images', 'comments.images')->findOrFail($id);

        DB::beginTransaction();

        try {
            // Delete all video images
            foreach ($video->images as $img) {
                \Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }

            // Delete comments + comment images
            foreach ($video->comments as $comment) {
                foreach ($comment->images as $img) {
                    \Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
                $comment->delete();
            }

            // Delete the video itself
            $video->delete();

            DB::commit();

            return response()->json(['message' => 'Video deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }





    public function index(Request $request)
{
    // Optional: pagination size
    $perPage = $request->get('per_page', 10);

    // Fetch all videos with all nested relations
    $videos = Video::with([
        'images',
        'comments.user',
        'comments.images'
    ])
    ->latest()
    ->paginate($perPage);

    return response()->json([
        'message' => 'Video list fetched successfully',
        'data'    => $videos
    ]);
}

public function listAll()
{
    $videos = Video::with([
        'images',
        'comments.user',
        'comments.images'
    ])->get();

    return response()->json($videos);
}


Route::post('/videos', [VideoController::class, 'store']);
Route::post('/videos/{id}', [VideoController::class, 'update']);
Route::delete('/videos/{id}', [VideoController::class, 'destroy']);

// Comments
Route::post('/comments', [CommentController::class, 'store']);
Route::post('/comments/{id}', [CommentController::class, 'update']);
Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

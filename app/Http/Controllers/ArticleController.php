<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Role\MedicalFacility;
use App\Models\MedicalService;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->only(['publish', 'unpublish','store','destroy','update']);
    }
    public function index()
    {
        return Article::all();
    }

    public function articleHomepage()
    {
        $top_famous = Article::topFavorite(5)->get();
        $recent = Article::recent(5)->get();
        return response()->json([
            'top_famous' => $top_famous,
            'recent' => $recent,
        ]);
    }

    public function show(Article $article)
    {
        return $article;
    }

    public function showByPatient(Article $article)
    {
        $article->update(['views' => $article->views + 1]);
        $recommend_article = Article::inRandomOrder()->limit(5)->get();
        $services = MedicalService::inRandomOrder()->limit(3)->get();
        return response()->json([
            'article' => $article,
            'recommend_article' => $recommend_article,
            'services' => $services,
            'message' => 'Article retrieved successfully',
        ]);
    }

    public function store(Request $request)
    {
        // Validate and store the article
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:draft,published',
        ]);

        if($request->hasFile('cover_image')) {
            $validatedData['cover_image'] = cloudinary()->upload($request->file('cover_image')->getRealPath(), [
                'folder' => 'articles',
                'overwrite' => true,
            ])->getSecurePath();
        }

        // Store the article in the database (pseudo code)
        Article::create($validatedData);

        return response()->json([
            'message' => 'Article created successfully',
        ]);
    }

    public function publish(Article $article)
    {
        // Publish the article (pseudo code)
        $article->update(['status' => 'published']);

        return response()->json([
            'article' => $article,
            'message' => 'Article published successfully',
        ]);
    }

    public function unpublish(Article $article)
    {
        // Draft the article (pseudo code)
        $article->update(['status' => 'draft']);

        return response()->json([
            'message' => 'Article drafted successfully',
        ]);
    }

    public function destroy(Article $article)
    {
        // Delete the article (pseudo code)
        $article->delete();

        return response()->json([
            'message' => 'Article deleted successfully',
        ]);
    }

    public function update(Request $request, Article $article)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:draft,published',
            'cover_image' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string'
        ]);

        // Handle cover image upload if a new file is provided
        if($request->hasFile('cover_image')) {
            $validatedData['cover_image'] = cloudinary()->upload($request->file('cover_image')->getRealPath(), [
                'folder' => 'articles',
                'overwrite' => true,
            ])->getSecurePath();
        }

        // Update the article
        $article->update($validatedData);

        return response()->json([
            'article' => $article,
            'message' => 'Article updated successfully',
        ]);
    }
}

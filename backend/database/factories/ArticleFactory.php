<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $vnfaker = vnfaker();

        return [
            'title' => $vnfaker->sentences(1, false),
            'cover_image' => 'https://loremflickr.com/800/600/nature?' . mt_rand(),
            'content' => $vnfaker->paragraphs(3, false, "<br>"),
            'status' => 'published',
            'views' => $vnfaker->numberBetween(0, 1000),
        ];
    }
}

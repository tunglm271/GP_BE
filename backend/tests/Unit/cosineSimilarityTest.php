<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\DiagnosisController;

class cosineSimilarityTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new DiagnosisController();
    }

    public function test_cosine_similarity_perfect_match()
    {
        $vec1 = [1, 2, 3];
        $vec2 = [1, 2, 3];

        $similarity = $this->controller->cosineSimilarity($vec1, $vec2);
        $this->assertEqualsWithDelta(1.0, $similarity, 0.0001);
    }

    public function test_cosine_similarity_orthogonal_vectors()
    {
        $vec1 = [1, 0];
        $vec2 = [0, 1];

        $similarity = $this->controller->cosineSimilarity($vec1, $vec2);
        $this->assertEqualsWithDelta(0.0, $similarity, 0.0001);
    }

    public function test_cosine_similarity_zero_vector()
    {
        $vec1 = [0, 0];
        $vec2 = [1, 2];

        // Khi vector zero, tránh chia 0 -> kết quả là 0 do +1e-10
        $similarity = $this->controller->cosineSimilarity($vec1, $vec2);
        $this->assertEqualsWithDelta(0.0, $similarity, 0.0001);
    }
}

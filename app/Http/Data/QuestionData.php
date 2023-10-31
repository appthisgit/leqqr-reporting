<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class QuestionData extends Data
{

    public function __construct(
        public readonly string $question,
        public readonly string $answer,
    ) { }
}

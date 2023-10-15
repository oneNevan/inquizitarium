<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\Service;

use App\Quiz\Checker\Domain\ValueObject\AnsweredQuestion;
use App\Quiz\Checker\Domain\ValueObject\CheckedQuestion;

interface QuestionCheckingPolicyInterface
{
    public function check(AnsweredQuestion $question): CheckedQuestion;
}

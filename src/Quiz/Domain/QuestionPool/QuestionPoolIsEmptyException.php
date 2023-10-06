<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuestionPool;

use App\Quiz\Creator\QuizCreatorExceptionInterface;

final class QuestionPoolIsEmptyException extends \Exception implements QuizCreatorExceptionInterface
{
    public function __construct()
    {
        parent::__construct('Question pool is empty');
    }
}

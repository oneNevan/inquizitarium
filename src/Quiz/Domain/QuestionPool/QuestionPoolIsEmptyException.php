<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuestionPool;

final class QuestionPoolIsEmptyException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Question pool is empty');
    }
}

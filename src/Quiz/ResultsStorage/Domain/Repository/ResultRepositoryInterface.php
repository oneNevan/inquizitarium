<?php

declare(strict_types=1);

namespace App\Quiz\ResultsStorage\Domain\Repository;

use App\Quiz\Checker\Domain\Entity\CheckedQuiz;

/**
 * TODO: reading is not necessary, adding (saving) results is enough for now.
 */
interface ResultRepositoryInterface
{
    public function add(CheckedQuiz $quiz): void;

    public function countAll(): int;
}

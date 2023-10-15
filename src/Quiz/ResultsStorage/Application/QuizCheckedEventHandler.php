<?php

declare(strict_types=1);

namespace App\Quiz\ResultsStorage\Application;

use App\Quiz\Checker\Domain\QuizCheckedEvent;
use App\Quiz\ResultsStorage\Domain\Repository\ResultRepositoryInterface;

final readonly class QuizCheckedEventHandler
{
    public function __construct(
        private ResultRepositoryInterface $repository,
    ) {
    }

    public function __invoke(QuizCheckedEvent $event): void
    {
        $this->repository->add($event->getQuiz());
    }
}

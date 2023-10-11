<?php

declare(strict_types=1);

namespace App\Quiz\RestApi;

use App\Quiz\Checker\CheckQuiz;
use App\Quiz\Creator\CreateQuiz;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class ApiController
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('api/v1/quiz', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateQuiz $command = null): JsonResponse
    {
        $command ??= new CreateQuiz();

        return new JsonResponse($this->normalizer->normalize($this->executeCommand($command)));
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('api/v1/quiz/check', methods: ['POST'])]
    public function check(#[MapRequestPayload] CheckQuiz $command): JsonResponse
    {
        return new JsonResponse($this->normalizer->normalize($this->executeCommand($command)));
    }

    private function executeCommand(object $command): object
    {
        $result = $this->commandBus->dispatch($command)->last(HandledStamp::class)?->getResult();
        if (!is_object($result)) {
            throw new \LogicException('Failed to retrieve a result object from command handler.');
        }

        return $result;
    }
}

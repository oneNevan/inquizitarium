<?php

declare(strict_types=1);

namespace App\Quiz\RestApi;

use App\Quiz\Checker\CheckQuiz;
use App\Quiz\Creator\CreateQuiz;
use App\Quiz\Domain\CheckedQuiz\Quiz as CheckedQuiz;
use App\Quiz\Domain\NewQuiz\Quiz as CreatedQuiz;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * TODO: instead of referencing Command classes and Domain entities,
 *  the API should ideally expose its own DTO objects..
 *  Separate DTO within API namespace would provide more flexibility for public API documentation
 *  without messing Commands and Domain objects with unrelated attributes/annotations required for OpenApi...
 *  So that the API should be built on top of the Domain, but not wise versa.
 *
 *  But for now it's fine..
 *  So far I faced just one issue with serializer forcing me to use #[SerializedName] attribute inside the Domain...
 *
 * @see \App\Quiz\Domain\SolvedQuiz\AnswerOption::__construct
 */
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
    #[OA\Tag('Quiz Creator')]
    #[OA\RequestBody(content: new Model(type: CreateQuiz::class))]
    #[OA\Response(
        response: 201,
        description: 'New quiz successfully created',
        content: new Model(type: CreatedQuiz::class),
    )]
    #[Route('api/v1/quiz', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateQuiz $command = null): JsonResponse
    {
        $command ??= new CreateQuiz();

        return new JsonResponse($this->normalizer->normalize($this->executeCommand($command)), status: 201);
    }

    /**
     * @throws ExceptionInterface
     */
    #[OA\Tag('Quiz Checker')]
    #[OA\RequestBody(content: new Model(type: CheckQuiz::class))]
    #[OA\Response(
        response: 200,
        description: 'Quiz successfully checked',
        content: new Model(type: CheckedQuiz::class),
    )]
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

<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Infrastructure;

use App\Core\Application\CommandBusInterface;
use App\Quiz\Checker\Application\CheckQuiz;
use App\Quiz\Checker\Domain\Entity\CheckedQuiz;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
 * @see \App\Quiz\Checker\Domain\ValueObject\AnswerOption::__construct
 */
final readonly class ApiController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private NormalizerInterface $normalizer,
    ) {
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
        $checkedQuiz = $this->commandBus->execute($command, CheckedQuiz::class);

        return new JsonResponse($this->normalizer->normalize($checkedQuiz));
    }
}

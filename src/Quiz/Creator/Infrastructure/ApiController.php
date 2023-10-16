<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Infrastructure;

use App\Core\Application\CommandBusInterface;
use App\Quiz\Creator\Application\CreateQuiz;
use App\Quiz\Creator\Domain\Entity\Quiz;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class ApiController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     *
     * TODO: describe 400, 422 responses
     */
    #[OA\Tag('Quiz Creator')]
    #[OA\RequestBody(content: new Model(type: CreateQuiz::class))]
    #[OA\Response(
        response: 201,
        description: 'New quiz successfully created',
        content: new Model(type: Quiz::class),
    )]
    #[Route('api/v1/quiz/create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateQuiz $command = null): JsonResponse
    {
        $command ??= new CreateQuiz();

        $createdQuiz = $this->commandBus->execute($command, Quiz::class);

        return new JsonResponse($this->normalizer->normalize($createdQuiz), status: 201);
    }
}

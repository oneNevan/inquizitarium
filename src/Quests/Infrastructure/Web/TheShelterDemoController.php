<?php

declare(strict_types=1);

namespace App\Quests\Infrastructure\Web;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('', name: 'quests_the_shelter')]
final readonly class TheShelterDemoController
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,
    ) {
    }

    /**
     * NOTE: this is just a DEMO controller to demonstrate API usage with some web UI.
     *
     * I don't want to add twig or any other frontend tools for that purpose (at least for now).
     */
    public function __invoke(): Response
    {
        return new Response(content: file_get_contents($this->projectDir.'/templates/quests/the-shelter-demo.html'));
    }
}

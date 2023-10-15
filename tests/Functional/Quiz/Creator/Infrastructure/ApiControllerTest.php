<?php

declare(strict_types=1);

namespace App\Tests\Functional\Quiz\Creator\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    private const URI = 'api/v1/quiz/create';

    public function testCreateQuiz(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', 'api/v1/quiz/create', [
            'questionsCount' => 1,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');
        $data = $client->getResponse()->getContent();
        $newQuizId = json_decode($data, false, 512, JSON_THROW_ON_ERROR)->id;
        self::assertJsonStringEqualsJsonString(
            <<<JSON
            {
              "id": "$newQuizId",
              "questions": [
                {
                  "expression": "1 + 1",
                  "comparisonOperator": "=",
                  "answerOptions": ["3", "2", "0"]
                }
              ]
            }
            JSON,
            $data,
        );
    }

    public function invalidJsonUriProvider(): iterable
    {
        return [[self::URI]];
    }
}

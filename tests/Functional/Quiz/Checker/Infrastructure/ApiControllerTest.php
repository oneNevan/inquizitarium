<?php

declare(strict_types=1);

namespace App\Tests\Functional\Quiz\Checker\Infrastructure;

use App\Tests\Functional\InvalidJsonRequestTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    use InvalidJsonRequestTestCase;

    private const URI = 'api/v1/quiz/check';

    public function testCheckQuiz(): void
    {
        $quizId = UUid::uuid7()->toString();
        $payload = [
            'quizId' => $quizId,
            'questions' => [
                [
                    'expression' => '4 + 5',
                    'comparisonOperator' => '=',
                    'answers' => [
                        ['expression' => '3 + 8', 'selected' => false],
                        ['expression' => '6 + 3', 'selected' => true],
                        ['expression' => '2 + 5', 'selected' => false],
                        ['expression' => '9', 'selected' => true],
                    ],
                ],
            ],
        ];

        $client = self::createClient();
        $client->jsonRequest('POST', self::URI, $payload);

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');
        self::assertJsonStringEqualsJsonString(
            <<<JSON
            {
              "quizId": "$quizId",
              "questions": [
                {
                  "expression": "4 + 5",
                  "comparisonOperator": "=",
                  "answers": [
                    {
                      "expression": "3 + 8",
                      "correct": null
                    },
                    {
                      "expression": "6 + 3",
                      "correct": true
                    },
                    {
                      "expression": "2 + 5",
                      "correct": null
                    },
                    {
                      "expression": "9",
                      "correct": true
                    }
                  ],
                  "answerCorrect": true
                }
              ],
              "passed": true
            }
            JSON,
            $client->getResponse()->getContent(),
        );
    }

    public function testCheckQuizValidationFailed(): void
    {
        $payload = [
            'quizId' => UUid::uuid7()->toString(),
            'questions' => [
                [
                    'expression' => '4 + 5',
                    'comparisonOperator' => '=',
                    'answers' => [
                        // missing answers
                    ],
                ],
            ],
        ];

        $client = self::createClient();
        $client->jsonRequest('POST', self::URI, $payload);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseFormatSame('json');
        self::assertJsonStringEqualsJsonString(
            <<<JSON
            {
              "type": "https:\/\/symfony.com\/errors\/validation",
              "title": "Validation Failed",
              "status": 422,
              "detail": "questions[0].answers: This value should not be blank.",
              "violations": [
                {
                  "propertyPath": "questions[0].answers",
                  "title": "This value should not be blank.",
                  "template": "This value should not be blank.",
                  "parameters": {
                    "{{ value }}": "array"
                  },
                  "type": "urn:uuid:c1051bb4-d103-4f74-8988-acbcafc7fdc3"
                }
              ]
            }
            JSON,
            $client->getResponse()->getContent()
        );
    }

    public function invalidJsonUriProvider(): iterable
    {
        return [[self::URI]];
    }
}

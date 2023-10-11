<?php

declare(strict_types=1);

namespace App\Tests\Functional\Quiz\RestApi;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testCreateQuiz(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', 'api/v1/quiz', [
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
                  "expression": "1+1",
                  "comparisonOperator": "=",
                  "answerOptions": ["3", "2", "0"]
                }
              ]
            }
            JSON,
            $data,
        );
    }

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
                        ['expression' => '3 + 8', 'isSelected' => false],
                        ['expression' => '6 + 3', 'isSelected' => true],
                        ['expression' => '2 + 5', 'isSelected' => false],
                        ['expression' => '9', 'isSelected' => true],
                    ],
                ],
            ],
        ];

        $client = self::createClient();
        $client->jsonRequest('POST', 'api/v1/quiz/check', $payload);

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

    /**
     * @testWith ["api/v1/quiz"]
     *           ["api/v1/quiz/check"]
     */
    public function testBadRequestError(string $uri): void
    {
        $client = self::createClient();
        $client->request('POST', $uri, server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], content: 'not a valid json');

        self::assertResponseStatusCodeSame(400);
        self::assertResponseFormatSame('json');
        self::assertJsonStringEqualsJsonString(
            <<<JSON
            {
              "type": "https:\/\/tools.ietf.org\/html\/rfc2616#section-10",
              "title": "An error occurred",
              "status": 400,
              "detail": "Bad Request"
            }
            JSON,
            $client->getResponse()->getContent()
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
        $client->jsonRequest('POST', 'api/v1/quiz/check', $payload);

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
}

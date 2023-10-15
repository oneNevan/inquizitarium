<?php

declare(strict_types=1);

namespace App\Tests\Functional;

trait InvalidJsonRequestTestCase
{
    /**
     * @dataProvider invalidJsonUriProvider
     */
    public function testBadRequestErrorWhenInvalidJson(string $uri): void
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
}

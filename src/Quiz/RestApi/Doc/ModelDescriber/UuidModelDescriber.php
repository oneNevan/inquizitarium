<?php

declare(strict_types=1);

namespace App\Quiz\RestApi\Doc\ModelDescriber;

use Nelmio\ApiDocBundle\Model\Model;
use Nelmio\ApiDocBundle\ModelDescriber\ModelDescriberInterface;
use OpenApi\Annotations\Schema;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidModelDescriber implements ModelDescriberInterface
{
    public function describe(Model $model, Schema $schema): void
    {
        $schema->title = 'Uuid';
        $schema->type = 'string';
        $schema->description = 'A universally unique identifier per RFC 4122';
        // generating an example in runtime instead of static value is intentional...
        // it provides a different valid value each time when Swagger web UI gets rendered
        // so that user can just keep this value when testing API via Swagger web UI
        $schema->example = Uuid::uuid7(new \DateTimeImmutable())->toString();
    }

    public function supports(Model $model): bool
    {
        return is_a($model->getType()->getClassName(), UuidInterface::class, allow_string: true);
    }
}

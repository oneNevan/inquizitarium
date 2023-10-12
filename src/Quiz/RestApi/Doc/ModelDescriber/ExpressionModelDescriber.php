<?php

declare(strict_types=1);

namespace App\Quiz\RestApi\Doc\ModelDescriber;

use App\Math\Domain\Expression\ExpressionInterface;
use Nelmio\ApiDocBundle\Model\Model;
use Nelmio\ApiDocBundle\ModelDescriber\ModelDescriberInterface;
use OpenApi\Annotations\Schema;

class ExpressionModelDescriber implements ModelDescriberInterface
{
    public function describe(Model $model, Schema $schema): void
    {
        $schema->title = 'Expression';
        $schema->type = 'string';
        $schema->description = 'A valid math expression';
        $schema->example = '2 + 2';
    }

    public function supports(Model $model): bool
    {
        return is_a($model->getType()->getClassName(), ExpressionInterface::class, allow_string: true);
    }
}

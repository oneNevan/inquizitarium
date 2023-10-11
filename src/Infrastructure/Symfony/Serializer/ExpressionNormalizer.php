<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use App\Math\Domain\Expression\Expression;
use App\Math\Domain\Expression\ExpressionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExpressionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            ExpressionInterface::class => true,
        ];
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Expression
    {
        if (!is_string($data) || !is_a($type, ExpressionInterface::class, allow_string: true)) {
            throw new InvalidArgumentException(sprintf('Unable to denormalize an object of type "%s" from given data of type "%s".', $type, get_debug_type($data)));
        }

        return new Expression($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_a($type, ExpressionInterface::class, allow_string: true);
    }

    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        if (!$object instanceof ExpressionInterface) {
            throw new InvalidArgumentException(sprintf('Data expected to be an instance of %s, "%s" given.', ExpressionInterface::class, get_debug_type($object)));
        }

        return (string) $object;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof ExpressionInterface;
    }
}

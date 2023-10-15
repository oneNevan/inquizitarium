<?php

declare(strict_types=1);

namespace App\Math\Infrastructure\Symfony\Serializer;

use App\Math\Domain\ValueObject\Expression;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExpressionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            Expression::class => true,
        ];
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Expression
    {
        if (!is_string($data) || !is_a($type, Expression::class, allow_string: true)) {
            throw new InvalidArgumentException(sprintf('Unable to denormalize an object of type "%s" from given data of type "%s".', $type, get_debug_type($data)));
        }

        try {
            return Expression::new($data);
        } catch (\Throwable $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_a($type, Expression::class, allow_string: true);
    }

    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        if (!$object instanceof Expression) {
            throw new InvalidArgumentException(sprintf('Data expected to be an instance of %s, "%s" given.', Expression::class, get_debug_type($object)));
        }

        return (string) $object;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Expression;
    }
}

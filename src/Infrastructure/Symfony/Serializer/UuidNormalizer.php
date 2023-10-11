<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Serializer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UuidNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            UuidInterface::class => true,
        ];
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): UuidInterface
    {
        if (!is_string($data) || !is_a($type, UuidInterface::class, allow_string: true) || !Uuid::isValid($data)) {
            throw new InvalidArgumentException(sprintf('Unable to denormalize an object of type "%s" from given data of type "%s".', $type, get_debug_type($data)));
        }

        return Uuid::fromString($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_a($type, UuidInterface::class, allow_string: true);
    }

    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        if (!$object instanceof UuidInterface) {
            throw new InvalidArgumentException(sprintf('Data expected to be an instance of %s, "%s" given.', UuidInterface::class, get_debug_type($object)));
        }

        return $object->toString();
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof UuidInterface;
    }
}

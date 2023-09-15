<?php

// src/Normalizer/ErrorNormalizer.php

namespace App\Normalizer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ErrorNormalizer implements NormalizerInterface
{
    public function normalize($exception, string $format = null, array $context = []): array
    {
        return [
            'message' => $context['debug'] ? $exception->getMessage() : 'An error occured',
            'status' => $exception->getStatusCode(),
            'trace' => $context['debug'] ? $exception->getTrace() : [],
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }
}
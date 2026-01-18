<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HealthController
{
    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
        ], 200);
    }
}
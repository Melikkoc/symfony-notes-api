<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\AppInfoService;

class VersionController {

    #[Route('/api/version', name: 'api_version', methods: ['GET'])]
    public function __invoke(AppInfoService $service): JsonResponse {
        return new JsonResponse(
            $service->getVersion()
        , 200);
    }
}
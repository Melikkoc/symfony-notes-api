<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController {
    #[Route('/lucky/number')]
    public function number(): JsonResponse {
        $number = random_int(0, 100);

        return new JsonResponse ([
            'number' => $number,
        ]);
    }
}
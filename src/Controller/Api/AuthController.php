<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\RegisterUserRequestDto;
use App\Service\RegisterUserService;
use App\Exception\UserAlreadyExistsException;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function registerUser(Request $request, ValidatorInterface $validator, RegisterUserService $service):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON Body'], 400);
        }

        $dto = new RegisterUserRequestDto();
        
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            $formattedErrors = [];
            foreach ($errors as $error) {
                $field = $error->getPropertyPath();
                $formattedErrors[$field][] = $error->getMessage();
            }
            return $this->json(['errors' => $formattedErrors], 422 );
        }

        try {
        $service->registerUser($dto);
        } catch (UserAlreadyExistsException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                409
            );    
        }

        return $this->json(['message' => 'User registered successfully'], 201);
    }
}
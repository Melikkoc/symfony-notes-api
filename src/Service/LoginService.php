<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Exception\LoginFailedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Dto\LoginRequestDto;

class LoginService {
    private EntityManagerInterface $em;    
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    public function loginUser(LoginRequestDto $dto):void 
    {
        $user = $this->em->getRepository(User::class)
                ->findOneBy(['email' => $dto->email]);

        if (!$user) {
            throw new LoginFailedException('Invalid credentials');
        } 

        $plainPassword = $dto->password;
        $passwordValid = $this->passwordHasher->isPasswordValid($user, $plainPassword);
        
        if (!$passwordValid) {
            throw new LoginFailedException('Invalid credentials');
        }
    }
}
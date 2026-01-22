<?php
namespace App\Service;
 
use App\Entity\User;
use App\Dto\RegisterUserRequestDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Exception\UserAlreadyExistsException;

class RegisterUserService {
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;
    
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }
    
    public function registerUser(RegisterUserRequestDto $dto): User 
    {
        $user = new User();

        $emailCheck = $this->em->getRepository(User::class)->findOneBy([
            'email' => $dto->email
        ]);

        if ($emailCheck !== null) {
            throw new UserAlreadyExistsException(
                'User already exists with email: ' . $dto->email
            );
        }

        $user->setEmail($dto->email);

        $plainPassword = $dto->password;
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);


        $this->em->persist($user);
        $this->em->flush();

        return $user;
    } 
}
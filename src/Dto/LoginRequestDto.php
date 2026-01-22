<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequestDto {

    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[Assert\Length(
        max: 180,
    )]
    public string $email;
    
    #[Assert\NotBlank()]
    #[Assert\Length(
        min: 8,
    )]
    public string $password;
}
<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateNoteRequestDto {

    #[Assert\NotBlank()]
    #[Assert\Length(
        min: 3,
        max: 100,
    )]
    public string $title;
    
    #[Assert\NotBlank()]
    #[Assert\Length(
        min: 10,
    )]
    public string $content;
}
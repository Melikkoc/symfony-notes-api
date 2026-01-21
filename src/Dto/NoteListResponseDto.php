<?php

namespace App\Dto\Response;

use App\Dto\Response\NoteResponseDto;
use App\Dto\Response\PaginationMetaDto;

class NoteListResponseDto {
    
    public PaginationMetaDto $meta;
    /**
     * @var NoteResponseDto[]
     */
    public array $items = [];    
}
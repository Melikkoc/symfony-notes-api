<?php

namespace App\Dto\Response;

class NoteListResponseDto {
    
    public PaginationMetaDto $meta;
    /**
     * @var NoteResponseDto[]
     */
    public array $items = [];    
}
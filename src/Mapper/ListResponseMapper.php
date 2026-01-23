<?php
namespace App\Mapper;

use App\Dto\Response\PaginationMetaDto;
use App\Dto\Response\NoteListResponseDto;
use App\Mapper\NoteResponseMapper;

class ListResponseMapper{
   
    private NoteResponseMapper $noteMapper;

    public function __construct(NoteResponseMapper $noteMapper)
    {
        $this->noteMapper = $noteMapper;
    }

    public function mapList(array $notes, int $page, int $limit, int $total):NoteListResponseDto {

        $listDto = new NoteListResponseDto;
        
        $metaDto = new PaginationMetaDto;
        $metaDto->page = $page;
        $metaDto->limit = $limit;
        $metaDto->total = $total;

        $listDto->meta = $metaDto;

        foreach ($notes as $note) {
            $listDto->items[] = $this->noteMapper->mapNote($note);
        }

        return $listDto;
    }
}
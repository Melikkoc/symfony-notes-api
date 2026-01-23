<?php
namespace App\Mapper;

use App\Entity\Note;
use App\Dto\Response\NoteResponseDto;

class NoteResponseMapper
{

    public function mapNote(Note $note):NoteResponseDto {

        $dto = new NoteResponseDto;

        $dto->id = $note->getId();
        $dto->title = $note->getTitle();
        $dto->content = $note->getContent();
        $dto->createdAt = $note->getCreatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }
}
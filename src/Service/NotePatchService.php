<?php
namespace App\Service;

use App\Dto\UpdateNoteRequestDto;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\NoteNotFoundException;

class NotePatchService {

    private EntityManagerInterface $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function patchNote(int $id, UpdateNoteRequestDto $dto): Note
    {
        $note = $this->em->getRepository(Note::class)->find($id);

        if (!$note) {
            throw new NoteNotFoundException('No note found for id'.$id);
        }
        
        if ($dto->title !== null) {
            $note->setTitle($dto->title);
        }
        
        if ($dto->content !== null) {
            $note->setContent($dto->content);
        }
     
        $this->em->flush();

        return $note;
    }
}
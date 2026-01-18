<?php
namespace App\Service;

use App\Dto\CreateNoteRequestDto;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;

class NoteCreateService {
    private EntityManagerInterface $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

public function createNote(CreateNoteRequestDto $dto): Note
{

    $note = new Note();
    $note->setTitle($dto->title);
    $note->setContent($dto->content);
    $note->setCreatedAt(new \DateTimeImmutable());

    $this->em->persist($note);
    $this->em->flush();

    return $note;
}
}
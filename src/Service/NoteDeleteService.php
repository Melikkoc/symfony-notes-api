<?php
namespace App\Service;

use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\NoteNotFoundException;

class NoteDeleteService {
    
    private EntityManagerInterface $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function deleteNote(int $id) 
    {
        $note = $this->em->getRepository(Note::class)->find($id);

        if (!$note) {
            throw new NoteNotFoundException('No Note found for id ' . $id);
        }

        $this->em->remove($note);
        $this->em->flush();
    }
}
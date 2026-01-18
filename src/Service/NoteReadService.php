<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Note;

class NoteReadService {

    private EntityManagerInterface $em;    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function readNote(int $id)
    {

        $repository = $this->em->getRepository(Note::class); 
        $note = $repository->find($id);

        return $note;
    }
}
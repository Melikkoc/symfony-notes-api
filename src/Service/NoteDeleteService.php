<?php
namespace App\Service;

use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\NoteNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;

class NoteDeleteService {
    private EntityManagerInterface $em;    
    private Security $security;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function deleteNote(int $id): void 
    {
        $user = $this->security->getUser();

        $note = $this->em->getRepository(Note::class)->find($id);

        if (!$note) {
            throw new NoteNotFoundException('No Note found for id ' . $id);
        }

        if ($note->getOwner() !== $user){
            throw new NoteNotFoundException('No Note found for id ' . $id);
        }

        $this->em->remove($note);
        $this->em->flush();
    }
}
<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Note;
use App\Exception\NoteNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;

class NoteReadService {
    private EntityManagerInterface $em;    
    private Security $security;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function readNote(int $id)
    {
        $user = $this->security->getUser();

        $note = $this->em->getRepository(Note::class)->find($id);

        if (!$note || $note->getOwner() !== $user){
            throw new NoteNotFoundException();
        }

        return $note;
    }
}
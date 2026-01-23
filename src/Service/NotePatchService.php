<?php
namespace App\Service;

use App\Dto\UpdateNoteRequestDto;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\NoteNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;

class NotePatchService {
    private EntityManagerInterface $em;    
    private Security $security;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function patchNote(int $id, UpdateNoteRequestDto $dto): Note
    {
        $user = $this->security->getUser();
        
        if (!$user instanceof \App\Entity\User) {
            throw new NoteNotFoundException('No note found for id ' . $id);
        
            }        
        $note = $this->em->getRepository(Note::class)->find($id);

        if (!$note) {
            throw new NoteNotFoundException('No note found for id ' . $id);
        }
        
        if ($note->getOwner()?->getId() !== $user->getId()) {
            throw new NoteNotFoundException('No Note found for id ' . $id);
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
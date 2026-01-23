<?php
namespace App\Service;

use App\Dto\CreateNoteRequestDto;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class NoteCreateService {
    private EntityManagerInterface $em;
    private Security $security;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

public function createNote(CreateNoteRequestDto $dto): Note
{

    $user = $this->security->getUser();

    if (!$user instanceof \App\Entity\User) {
        throw new AccessDeniedException('User must be authenticated to create a note.');
    }

    $note = new Note();
    $note->setOwner($user);
    $note->setTitle($dto->title);
    $note->setContent($dto->content);
    $note->setCreatedAt(new \DateTimeImmutable());

    $this->em->persist($note);
    $this->em->flush();

    return $note;
}
}
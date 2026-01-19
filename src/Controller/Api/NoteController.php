<?php

namespace App\Controller\Api;

use App\Dto\CreateNoteRequestDto;
use App\Dto\UpdateNoteRequestDto;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\NoteCreateService;
use App\Service\NoteReadService;
use App\Service\NoteListService;
use App\Service\NotePatchService;
use App\Service\NoteDeleteService;
use App\Exception\NoteNotFoundException;

class NoteController extends AbstractController
{
    #[Route('/api/note', name: 'api_note', methods: ['POST'])]
    public function createNoteRequest(Request $request, ValidatorInterface $validator, NoteCreateService $noteCreateService ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(
                ['error' => 'Invalid JSON Body'],
                400
            );
        }

        $dto = new CreateNoteRequestDto();

        $dto->title = $data['title'] ?? '';
        $dto->content = $data['content'] ?? '';

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            $formattedErrors = [];
            foreach ($errors as $error) {
                $field = $error->getPropertyPath();
                $formattedErrors[$field][] = $error->getMessage();
            }
            return $this->json(['errors' => $formattedErrors], 422 );
        }
        $note = $noteCreateService->createNote($dto);
            
        return $this->json([
        'id' => $note->getId(),
        'title' => $note->getTitle(),
        'content' => $note->getContent(),
        'createdAt' => $note->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }
    #[Route('/api/note/{id}', name: 'get_note', methods:['GET'])]
    public function getNotebyId(int $id, NoteReadService $noteReadService): JsonResponse
    {
        $note = $noteReadService->readNote($id);

        if ($note === null) {
            return $this->json(['error' => 'Note not found'], 404);
        } else {
        return $this->json([
        'id' => $note->getId(),
        'title' => $note->getTitle(),
        'content' => $note->getContent(),
        'createdAt' => $note->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 200);
        }
    }

    #[Route('/api/note', name: 'get_notes', methods:['GET'])]
    public function getNotes(Request $request, NoteListService $noteList): JsonResponse
    {

        $page = (int) $request->query->get('page');
        $limit = (int) $request->query->get('limit');
        $sortBy = (string) $request->query->get('sortBy');
        $order = (string) $request->query->get('order');
        $search = $request->query->get('search');


        $notes = $noteList->listNotes($page, $limit, $sortBy, $order, $search);

        return $this->json([
            'notes' => $notes
        ], 200);
    }

    #[Route('/api/note/{id}', name: 'patch_note', methods:['PATCH'])]
    public function updateNote(int $id, Request $request, ValidatorInterface $validator, NotePatchService $notePatchService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!is_array($data)) {
            return $this->json(
                ['error' => 'Invalid JSON Body'],
                400
            );
        }

        $dto = new UpdateNoteRequestDto();

        $dto->title = $data['title'] ?? null;
        $dto->content = $data['content'] ?? null;
        
        if ($dto->title === null && $dto->content === null) {
            return $this->json(
                ['error' => 'Empty PATCH Body'],
                400
            );
        }        

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            $formattedErrors = [];
            foreach ($errors as $error) {
                $field = $error->getPropertyPath();
                $formattedErrors[$field][] = $error->getMessage();
            }
            return $this->json(['errors' => $formattedErrors], 422 );
        }

        try {
        $note = $notePatchService->patchNote($id, $dto);
        } catch (NoteNotFoundException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                404
            );    
        }

        return $this->json([
        'id' => $note->getId(),
        'title' => $note->getTitle(),
        'content' => $note->getContent(),
        'createdAt' => $note->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 200);
    }

    #[Route('/api/note/{id}', name: 'delete_note', methods:['DELETE'])]
    public function deleteNote(int $id, NoteDeleteService $noteDeleteService):JsonResponse
    {
        try {
            $noteDeleteService->deleteNote($id);
        } catch (NoteNotFoundException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                404
            );    
        }

        return $this->json(null, 204);
    }
}
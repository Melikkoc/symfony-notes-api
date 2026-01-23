<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Note;
use Symfony\Bundle\SecurityBundle\Security;

class NoteListService
{
    private EntityManagerInterface $em;
    private Security $security;

    private const DEFAULT_LIMIT = 10;
    private const MAX_LIMIT = 20;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @return array{
     *     notes: Note[],
     *     page: int,
     *     limit: int,
     *     total: int
     * }
     */
    public function listNotes(
        int $page,
        int $limit,
        string $sortBy,
        string $order,
        ?string $search
    ): array {
        if ($page < 1) {
            $page = 1;
        }

        if ($limit <= 0) {
            $limit = self::DEFAULT_LIMIT;
        }

        $limit = min($limit, self::MAX_LIMIT);
        $offset = ($page - 1) * $limit;

        $sortMap = [
            'id' => 'n.id',
            'title' => 'n.title',
            'createdAt' => 'n.createdAt',
        ];

        if (!isset($sortMap[$sortBy])) {
            $sortBy = 'createdAt';
        }

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $user = $this->security->getUser();

        /** ITEMS QUERY */
        $itemsQb = $this->em->createQueryBuilder()
            ->select('n')
            ->from(Note::class, 'n')
            ->where('n.owner = :user')
            ->setParameter('user', $user);

        if ($search !== null && $search !== '') {
            $itemsQb
                ->andWhere('n.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $itemsQb
            ->orderBy($sortMap[$sortBy], $order)
            ->addOrderBy('n.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $notes = $itemsQb->getQuery()->getResult();

        /** COUNT QUERY */
        $countQb = $this->em->createQueryBuilder()
            ->select('COUNT(n.id)')
            ->from(Note::class, 'n')
            ->where('n.owner = :user')
            ->setParameter('user', $user);

        if ($search !== null && $search !== '') {
            $countQb
                ->andWhere('n.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        return [
            'notes' => $notes,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ];
    }
}
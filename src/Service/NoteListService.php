<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class NoteListService 
{

    private EntityManagerInterface $em;
    private const DEFAULT_LIMIT = 10;
    private const MAX_LIMIT = 20;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function listNotes(
        int $page, 
        int $limit,
        string $sortBy,
        string $order,
        ?string $search
        ):array 
    {   

        if ($page < 1) {
            $page = 1;
        } 
        if ($limit <= 0) {
            $limit = self::DEFAULT_LIMIT;
        } 
        
        $limit = min($limit, self::MAX_LIMIT);
                
        $offset = ($page - 1) * $limit;

        $itemsQb = $this->em->createQueryBuilder();

        $sortList = [
            'id' => 'n.id',
            'title' => 'n.title',
            'createdAt' => 'n.createdAt',
        ];

        if (!array_key_exists($sortBy, $sortList)) {
            $sortBy = 'createdAt';
        }
        
        $sortOrder = ['ASC', 'DESC'];

        if (!in_array($order, $sortOrder)) {
            $order = 'DESC';
        }

        $itemsQb->select('n')
           ->from('App\Entity\Note', 'n');

        if ($search !== null && $search !== '') {   
           $itemsQb
           ->andWhere('n.title LIKE :search')
           ->setParameter('search', '%' . $search . '%');
        }

        $itemsQb->orderBy($sortList[$sortBy], $order)
           ->addOrderBy('n.id', 'DESC')
           ->setFirstResult( $offset )
           ->setMaxResults( $limit );


        $itemsQuery = $itemsQb->getQuery();
        $notes = $itemsQuery->getResult();
        
        $items = [];

        foreach ($notes as $note) {
            $items[] = [
                'id' => $note->getId(),
                'title' => $note->getTitle(),
                'content' => $note->getContent(),
                'createdAt' => $note->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        $countQb = $this->em->createQueryBuilder();

        $countQb->select('COUNT(n.id)')
                ->from('App\Entity\Note', 'n');
                
        if ($search !== null && $search !== '') {   
           $countQb
           ->andWhere('n.title LIKE :search')
           ->setParameter('search', '%' . $search . '%');
        }

        $countQuery = $countQb->getQuery();   
        $total = $countQuery->getSingleScalarResult();   

        $meta = [
            'page' => $page,
            'limit' => $limit,
            'total' => $total
        ];

    
        return [ 
            'meta' => $meta,   
            'items' => $items
        ];
    }
}
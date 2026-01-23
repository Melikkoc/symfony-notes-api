<?php
namespace App\Dto\Response;

class PaginationMetaDto {
    
    public int $page;
    public int $limit;
    public int $total;
}
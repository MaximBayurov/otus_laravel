<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

interface PaginationService
{
    public function getPagination(int $page): LengthAwarePaginator;
}

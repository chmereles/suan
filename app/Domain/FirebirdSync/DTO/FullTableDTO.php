<?php

namespace App\Domain\FirebirdSync\DTO;

class FullTableDTO
{
    public function __construct(
        public string $table,
        public array $columns,
        public int $count,
        public string $generated_at,
        public array $rows
    ) {}
}

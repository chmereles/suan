<?php

namespace App\Domain\FirebirdSync\DTO;

class IncrementalLogDTO
{
    public function __construct(
        public int $id,
        public string $table,
        public string $operation,
        public string $timestamp,
        public ?string $pk1,
        public ?string $pk2,
        public ?string $pk3,
        public array $changes
    ) {}
}

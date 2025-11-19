<?php

namespace App\Domain\FirebirdSync\DTO;

class ChangeDTO
{
    public function __construct(
        public string $column,
        public mixed $old,
        public mixed $new
    ) {}
}

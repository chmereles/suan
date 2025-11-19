<?php

namespace App\Domain\FirebirdSync\Contracts;

use App\Domain\FirebirdSync\DTO\FullTableDTO;

interface FullImporterInterface
{
    public function import(FullTableDTO $dto): void;
}

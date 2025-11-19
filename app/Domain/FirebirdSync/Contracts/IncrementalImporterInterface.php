<?php

namespace App\Domain\FirebirdSync\Contracts;

use App\Domain\FirebirdSync\DTO\IncrementalLogDTO;

interface IncrementalImporterInterface
{
    public function apply(IncrementalLogDTO $dto): void;
}

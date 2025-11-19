<?php

namespace App\Domain\FirebirdSync\Services;

use App\Domain\FirebirdSync\DTO\FullTableDTO;
use App\Infrastructure\FirebirdSync\Mappers\DatLaboralesMapper;
use App\Infrastructure\FirebirdSync\Mappers\LegajoMapper;
use App\Infrastructure\FirebirdSync\Mappers\PlaAgentesMapper;
use App\Infrastructure\FirebirdSync\Persistence\SuanAgentPersister;
use App\Infrastructure\FirebirdSync\Persistence\SuanLaborLinkPersister;
use App\Infrastructure\FirebirdSync\Persistence\SuanPersonPersister;

class FullSyncService
{
    public function __construct(
        private LegajoMapper $legajo,
        private DatLaboralesMapper $labor,
        private PlaAgentesMapper $agent,
        private SuanPersonPersister $person,
        private SuanLaborLinkPersister $link,
        private SuanAgentPersister $agentPersister,
    ) {}

    public function sync(FullTableDTO $dto)
    {
        foreach ($dto->rows as $row) {
            if ($dto->table === 'LEGAJOS') {
                $mapped = $this->legajo->map($row);
                $this->person->save($mapped);
            }

            if ($dto->table === 'DAT_LABORALES_PLA') {
                $mapped = $this->labor->map($row);
                $this->link->save($mapped);
            }

            if ($dto->table === 'PLA_AGENTES') {
                $mapped = $this->agent->map($row);
                $this->agentPersister->save($mapped);
            }
        }
    }
}

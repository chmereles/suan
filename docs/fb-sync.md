app/
└── Domain/
    └── FirebirdSync/
        ├── DTO/
        │   ├── FullTableDTO.php
        │   ├── IncrementalLogDTO.php
        │   └── ChangeDTO.php
        │
        ├── Contracts/
        │   ├── FullImporterInterface.php
        │   └── IncrementalImporterInterface.php
        │
        ├── Services/
        │   ├── FullSyncService.php
        │   ├── IncrementalSyncService.php
        │   └── FirebirdMapperService.php
        │
        ├── Actions/
        │   ├── ImportFullAction.php
        │   └── ImportIncrementalAction.php
        │
        └── Exceptions/
            ├── InvalidJsonException.php
            └── MappingException.php


└── Infrastructure/
    └── FirebirdSync/
        ├── Readers/
        │   ├── JsonFullReader.php
        │   └── JsonIncrementalReader.php
        │
        ├── Persisters/
        │   ├── SuanPersonPersister.php
        │   ├── SuanLaborLinkPersister.php
        │   └── SuanAgentPersister.php
        │
        ├── Mappers/
        │   ├── LegajoMapper.php
        │   ├── DatLaboralesMapper.php
        │   └── PlaAgentesMapper.php
        │
        └── Providers/
            └── FirebirdSyncProvider.php


app/Console/Commands/
    ├── FirebirdFullImportCommand.php
    └── FirebirdIncrementalImportCommand.php

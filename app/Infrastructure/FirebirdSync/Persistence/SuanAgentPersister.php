<?php

namespace App\Infrastructure\FirebirdSync\Persistence;

class SuanAgentPersister
{
    public function save(array $data)
    {
        dd('utilizar suan_labrol_link?');
        // \App\Models\SuanAgent::updateOrCreate(
        //     ['legacy_legajo_id' => $data['legacy_legajo_id']],
        //     $data
        // );
    }
}

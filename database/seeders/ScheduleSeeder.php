<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Labor\Models\SuanSchedule;
use App\Domain\Labor\Models\SuanScheduleWorkday;
use App\Domain\Attendance\Models\SuanLaborLink; // Ajustá namespace según tu estructura

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------------
        // 1. Crear horario base: Adm. Mañana 07:00–13:00
        // --------------------------------------------------
        $schedule = SuanSchedule::create([
            'name' => 'Administración Mañana',
            'description' => 'Horario estándar L–V 07:00–13:00 con 10 min tolerancia.',
            'type' => 'fixed',
            'active' => true,
        ]);

        // --------------------------------------------------
        // 2. Crear días L–V (Carbon: 1=lunes ... 5=viernes)
        // --------------------------------------------------
        $weekdays = [1, 2, 3, 4, 5];

        foreach ($weekdays as $day) {
            SuanScheduleWorkday::create([
                'schedule_id' => $schedule->id,
                'weekday' => $day,
                'segment_index' => 1,
                'start_time' => '07:00:00',
                'end_time' => '13:00:00',
                'tolerance_in_minutes' => 10,
                'tolerance_out_minutes' => 10,
                'is_working_day' => true,
            ]);
        }

        // --------------------------------------------------
        // 3. BONUS: Contrato parcial L–J 08–12
        // --------------------------------------------------
        $partial = SuanSchedule::create([
            'name' => 'Medio Tiempo Mañana',
            'description' => 'Solo L–J, 08:00–12:00',
            'type' => 'fixed',
            'active' => true,
        ]);

        foreach ([1, 2, 3, 4] as $day) {
            SuanScheduleWorkday::create([
                'schedule_id' => $partial->id,
                'weekday' => $day,
                'segment_index' => 1,
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'tolerance_in_minutes' => 5,
                'tolerance_out_minutes' => 5,
                'is_working_day' => true,
            ]);
        }

        // --------------------------------------------------
        // 4. Asignar el horario “Administración Mañana”
        //    a algunos suan_labor_links existentes
        // --------------------------------------------------
        $laborLinks = SuanLaborLink::take(10)->get();

        foreach ($laborLinks as $link) {
            $link->update(['schedule_id' => $schedule->id]);
        }
    }
}

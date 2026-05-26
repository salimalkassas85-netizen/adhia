<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'مدينة أبو حمص',
            'جواد حسني',
            'دمسنا',
            'بركة غطاس',
            'بسنتواي',
            'بلقطر',
            'كوم القناطر',
            'بطورس',
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(
                ['name' => $area],
                ['active' => true],
            );
        }
    }
}

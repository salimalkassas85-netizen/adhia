<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AreaAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@rowad.com'],
            [
                'name' => 'السوبر أدمن',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'area_id' => null,
            ],
        );

        $emails = [
            'مدينة أبو حمص' => 'hommos@rowad.com',
            'جواد حسني' => 'admin-gawad-hosny@adhia.local',
            'دمسنا' => 'admin-damsna@adhia.local',
            'بركة غطاس' => 'admin-barakat-ghattas@adhia.local',
            'بسنتواي' => 'admin-basantaway@adhia.local',
            'بلقطر' => 'admin-balqatar@adhia.local',
            'كوم القناطر' => 'admin-kom-el-qanater@adhia.local',
            'بطورس' => 'admin-btoros@adhia.local',
        ];

        Area::orderBy('id')->get()->each(function (Area $area) use ($emails): void {
            User::updateOrCreate(
                ['email' => $emails[$area->name] ?? 'admin-'.Str::slug($area->id.'-'.$area->name).'@adhia.local'],
                [
                    'name' => 'أدمن '.$area->name,
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'area_id' => $area->id,
                ],
            );
        });
    }
}

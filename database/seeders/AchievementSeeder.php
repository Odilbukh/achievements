<?php

namespace Database\Seeders;

use App\Enums\AchievementsEnum;
use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AchievementsEnum::toValues() as $key => $value) {
            Achievement::create(['name' => $value]);
        }
    }
}

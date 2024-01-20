<?php

namespace Database\Seeders;

use App\Enums\BadgeRequirementsEnum;
use App\Enums\BadgesEnum;
use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (BadgesEnum::toValues() as $key => $value) {
            $requirementsEnum = BadgeRequirementsEnum::toValues()[$key];

            Badge::create([
                'name' => $value,
                'requirement' => $requirementsEnum
            ]);
        }
    }
}

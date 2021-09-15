<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'All' => '‫الكل‬',
            'Agriculture' => '‫الزراعه‬',
            'Medical' => '‫الطب‬',
            'Electronics' => '‫إلكترونيات‬',
            'Technology' => '‫تكنولوجيا‬',
            'Automobile' => '‫سيارات‬',
            'Manufacturing' => '‫الصناعه‬',
            'Clothing' => '‫مالبس‬',
            'Real estate' => '‫عقارات‬',
            'Construction' => '‫بناء‬',
            'Beauty' => '‫تجميل‬',
            'Engineering' => '‫الهندسات‬',
            'Trade' => '‫تجاره‬',
            'Restaurants/Cafes' => '‫ومقاهي‬ ‫مطاعم‬',
            'Education' => '‫تعليم‬',
            'Ecommerce' => '‫تجاره‬ ‫الكترونيه‬',
            'Retail' => '‫متاجر‬',
            'Startup' => '‫أب‬ ‫ستارت‬'
        ];

        foreach ($categories as $en => $arab) {
            Category::create(['en' => $en, 'arab' => $arab]);
        }
    }
}

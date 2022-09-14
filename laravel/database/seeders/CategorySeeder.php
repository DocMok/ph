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
            'Agriculture' => 'الزراعه',
            'Medical' => 'الطب',
            'Electronics' => 'إلكترونيات',
            'Technology' => 'تكنولوجيا',
            'Automobile' => 'سيارات',
            'Manufacturing' => 'الصناعه',
            'Clothing' => 'الملابس',
            'Real estate' => 'عقارات',
            'Construction' => 'بناء',
            'Beauty' => 'تجميل',
            'Engineering' => 'الهندسات',
            'Trade' => 'تجاره',
            'Restaurants/Cafes' => 'المطاعم و المقاهي',
            'Education' => 'تعليم',
            'Ecommerce' => 'تجاره الكترونيه',
            'Retail' => 'متاجر',
            'Startup' => 'ستارت أب'
        ];

        foreach ($categories as $en => $arab) {
            Category::updateOrCreate(['en' => $en], ['en' => $en, 'arab' => $arab]);
        }
    }
}

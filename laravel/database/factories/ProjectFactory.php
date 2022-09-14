<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectOwner;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $currencies = ['usd', 'sdg'];
        return [
            'name' => $this->faker->text(50),
            'description' => $this->faker->text,
            'logo' => null,
            'currency' => $currencies[array_rand($currencies)],
            'amount_available' => rand(0, 100) * 1000,
            'amount_remaining' => rand(1, 100) * 1000,
            'category_id' => Category::all()->random()->id,
            'project_owner_id' => ProjectOwner::all()->random()->id
        ];
    }
}

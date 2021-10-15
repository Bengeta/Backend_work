<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->unique()->realTextBetween(15,25),
            'text' => $this->faker->realText(),
            'description' => $this->faker->realText(),
            'published_at' => $this->faker->dateTimeBetween('-2 months', "+2 weeks"),
            'is_published' => $this->faker->boolean(70),
            ];
    }
}

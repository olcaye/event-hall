<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $startTime = $this->faker->randomElement(['18:00', '19:00', '20:00', '21:00']);
        $date      = $this->faker->dateTimeBetween('+2 days', '+4 months');

        $coverImageNumber = $this->faker->numberBetween(1, 10);
        $coverImagePath   = "events/covers/{$coverImageNumber}.jpeg";

        $galleryImageCount = $this->faker->numberBetween(2, 5);
        $galleryNumbers    = $this->faker->randomElements(range(1, 10), $galleryImageCount);

        $galleryPaths = array_map(function ($number) {
            return "events/gallery/{$number}.jpeg";
        }, $galleryNumbers);

        return [
            'title'         => $this->faker->sentence(3),
            'description'   => $this->faker->paragraph(12),
            'location'      => $this->faker->address,
            'date'          => $date,
            'start_time'    => $startTime,
            'end_time'      => Carbon::parse($startTime)->addHours($this->faker->numberBetween(2, 5))->format('H:i'),
            'latitude'      => $this->faker->latitude,
            'longitude'     => $this->faker->longitude,
            'booking_limit' => $this->faker->optional(0.8)->numberBetween(20, 50),
            'cover_image'   => $coverImagePath,
            'gallery'       => $galleryPaths,
            'user_id'       => User::factory(),
            'status'        => EventStatus::ACTIVE,
        ];
    }
}

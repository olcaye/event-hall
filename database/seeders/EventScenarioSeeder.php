<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Enums\EventStatus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;

class EventScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Booking::truncate();
        Event::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Veritabanı (User, Event, Booking) temizlendi.');

        $users = User::factory(50)->create();
        $this->command->info("50 adet kullanıcı oluşturuldu.");

        $locations = array_merge(
            array_fill(0, 50, 'Istanbul'),
            array_fill(0, 50, 'Izmir')
        );
        shuffle($locations);

        $events = Event::factory(100)
            ->state(new Sequence(
                fn($sequence) => [
                    'user_id'   => $users->random()->id,
                    'location'  => $locations[$sequence->index] === 'Istanbul'
                        ? fake()->randomElement([
                            'Zorlu PSM – Beşiktaş',
                            'Harbiye Cemil Topuzlu Açıkhava Tiyatrosu – Şişli',
                            'Volkswagen Arena – Maslak',
                            'Babylon – Bomonti',
                            'Maximum UNIQ Hall – Maslak',
                            'Life Park – Bahçeköy',
                            'KüçükÇiftlik Park – Maçka',
                            'Parkorman – Maslak',
                            'Salt Galata – Karaköy',
                            'İstanbul Modern – Karaköy'
                        ]) . ', Istanbul'
                        : fake()->randomElement([
                            'Ahmed Adnan Saygun Sanat Merkezi – Balçova',
                            'İzmir Arena – Bayraklı',
                            'SoldOut Performance Hall – Bostanlı',
                            'İzmir Sanat – Konak',
                            'Tepekule Kongre ve Sergi Merkezi – Bayraklı',
                            'Hangout PSM – Alsancak',
                            'İzmir Fuar Kültürpark Açıkhava Tiyatrosu – Lozan Kapısı',
                            'Container Hall – Bornova',
                            'Atatürk Açıkhava Tiyatrosu – Konak',
                            'İzmir Enternasyonal Fuar Alanı – Kültürpark'
                        ]) . ', Izmir',
                    'latitude'  => $locations[$sequence->index] === 'Istanbul'
                        ? fake()->latitude(40.95, 41.05)
                        : fake()->latitude(38.35, 38.45),
                    'longitude' => $locations[$sequence->index] === 'Istanbul'
                        ? fake()->longitude(28.85, 29.15)
                        : fake()->longitude(27.05, 27.25),
                ]
            ))
            ->create();
        $this->command->info("100 adet etkinlik oluşturuldu (50 Istanbul, 50 Izmir).");

        $events->random(10)->each(function ($event) {
            $event->update([
                'date'   => fake()->dateTimeBetween('-3 months', '-1 week'),
                'status' => EventStatus::ENDED,
            ]);
        });
        $this->command->info("10 adet etkinliğin durumu 'Sona Erdi' olarak güncellendi.");

        $activeEvents = Event::where('status', EventStatus::ACTIVE)->get();
        $bookingCount = 0;

        foreach ($activeEvents as $event) {
            $potentialBookers = $users->where('id', '!=', $event->user_id);
            $numberOfBookings = rand(5, 20);
            $numberOfBookings = min($numberOfBookings, $potentialBookers->count());

            if ($numberOfBookings > 0) {
                $selectedUsers = $potentialBookers->random($numberOfBookings);

                if ($selectedUsers instanceof User) {
                    $selectedUsers = collect([$selectedUsers]);
                }

                foreach ($selectedUsers as $user) {
                    Booking::firstOrCreate(
                        [
                            'user_id'  => $user->id,
                            'event_id' => $event->id
                        ],
                    );
                    $bookingCount++;
                }
            }
        }
        $this->command->info("Aktif etkinlikler için toplam {$bookingCount} adet rezervasyon oluşturuldu.");
    }
}

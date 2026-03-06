<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@cebuevents.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]
        );

        // Sample events
        $events = [
            [
                'name' => 'Sinulog Festival 2026',
                'description' => 'The Sinulog Festival is an annual cultural and religious festival held in Cebu City. It is the biggest and grandest festival in the Philippines, celebrating the Filipino people\'s pagan origin and their acceptance of Roman Catholicism. The festival features street parades with participants in vibrant costumes performing the Sinulog dance.',
                'date_time' => '2026-01-18 08:00:00',
                'location' => 'Cebu City Sports Complex, Cebu City',
                'category' => 'Festival',
                'is_featured' => true,
            ],
            [
                'name' => 'Cebu Food Festival',
                'description' => 'A celebration of Cebuano cuisine featuring local delicacies, street food, and fine dining experiences. Join us for cooking demonstrations, food competitions, and unlimited tasting sessions from the best restaurants in Cebu.',
                'date_time' => '2026-02-14 10:00:00',
                'location' => 'SM Seaside City Cebu',
                'category' => 'Food',
                'is_featured' => true,
            ],
            [
                'name' => 'Acoustic Night at Crossroads',
                'description' => 'An intimate evening of live acoustic performances featuring local Cebuano artists. Enjoy great music, good food, and a relaxing atmosphere at one of Cebu\'s favorite hangout spots.',
                'date_time' => '2026-02-20 19:00:00',
                'location' => 'Crossroads, Banilad, Cebu City',
                'category' => 'Music',
                'is_featured' => true,
            ],
            [
                'name' => 'Digital Marketing Workshop',
                'description' => 'Learn the latest digital marketing strategies from industry experts. This hands-on workshop covers SEO, social media marketing, content creation, and paid advertising. Perfect for entrepreneurs and marketing professionals.',
                'date_time' => '2026-02-25 09:00:00',
                'location' => 'Cebu IT Park, Lahug',
                'category' => 'Workshop',
                'is_featured' => false,
            ],
            [
                'name' => 'Cebu Marathon 2026',
                'description' => 'Join thousands of runners in the annual Cebu Marathon! Choose from 5K, 10K, 21K, or full marathon distances. The route showcases Cebu\'s beautiful coastline and historic landmarks.',
                'date_time' => '2026-03-08 04:00:00',
                'location' => 'Cebu Business Park, Cebu City',
                'category' => 'Sports',
                'is_featured' => true,
            ],
            [
                'name' => 'Art in the Park',
                'description' => 'A weekend art fair featuring works from local and national artists. Browse paintings, sculptures, photographs, and handcrafted items. Art workshops and live painting sessions available.',
                'date_time' => '2026-03-15 10:00:00',
                'location' => 'Fuente Osmeña Circle, Cebu City',
                'category' => 'Art',
                'is_featured' => false,
            ],
            [
                'name' => 'Kadaugan sa Mactan',
                'description' => 'Commemorate the Battle of Mactan with cultural performances, reenactments, and festivities. This event celebrates the bravery of Lapu-Lapu and the rich history of Cebu.',
                'date_time' => '2026-04-27 08:00:00',
                'location' => 'Mactan Shrine, Lapu-Lapu City',
                'category' => 'Festival',
                'is_featured' => true,
            ],
            [
                'name' => 'Startup Pitch Night',
                'description' => 'Watch promising startups pitch their ideas to investors and industry experts. Network with entrepreneurs, investors, and tech enthusiasts in Cebu\'s growing startup ecosystem.',
                'date_time' => '2026-03-22 18:00:00',
                'location' => 'The Company, Cebu IT Park',
                'category' => 'Business',
                'is_featured' => false,
            ],
            [
                'name' => 'Beach Cleanup Drive',
                'description' => 'Join our community beach cleanup initiative! Help preserve Cebu\'s beautiful beaches and marine life. Gloves, bags, and refreshments provided. All ages welcome.',
                'date_time' => '2026-04-05 06:00:00',
                'location' => 'Mactan Newtown Beach, Lapu-Lapu City',
                'category' => 'Community',
                'is_featured' => false,
            ],
            [
                'name' => 'Jazz by the Bay',
                'description' => 'A sophisticated evening of jazz music by the waterfront. Featuring performances by acclaimed jazz musicians from Cebu and Manila. Dress code: Smart casual.',
                'date_time' => '2026-04-12 19:00:00',
                'location' => 'Nustar Resort & Casino, Cebu',
                'category' => 'Music',
                'is_featured' => true,
            ],
            [
                'name' => 'Photography Workshop',
                'description' => 'Master the art of photography with hands-on training from professional photographers. Learn composition, lighting, and post-processing techniques. Bring your own camera.',
                'date_time' => '2026-04-18 09:00:00',
                'location' => 'Tops Lookout, Busay, Cebu City',
                'category' => 'Workshop',
                'is_featured' => false,
            ],
            [
                'name' => 'Cebu Wine & Cheese Festival',
                'description' => 'An elegant evening of wine tasting paired with artisanal cheeses. Learn about different wine varieties and cheese pairings from sommeliers and cheese experts.',
                'date_time' => '2026-05-10 18:00:00',
                'location' => 'Marco Polo Plaza Hotel, Cebu',
                'category' => 'Food',
                'is_featured' => false,
            ],
        ];

        foreach ($events as $event) {
            Event::firstOrCreate(
                ['name' => $event['name']],
                $event
            );
        }
    }
}

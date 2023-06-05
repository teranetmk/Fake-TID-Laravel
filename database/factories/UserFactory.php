<?php

    use Faker\Generator as Faker;

    $factory->define(App\Models\User::class, function (Faker $faker) {
        return [
            'name' => $faker->name ?? 'arayik',
            'username' => $faker->username ?? 'arayik',
            'email' => $faker->unique()->safeEmail ?? 'arayik@example.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
            'remember_token' => str_random(10),
        ];
    });

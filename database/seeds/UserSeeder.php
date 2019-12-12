<?php


class UserSeeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        $users = [[
            'name' => 'Sergey',
            'email' => 'pariss8@mail.ru',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'remember_token' => str_random(),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]];
        for ($i = 0; $i < 10; $i++) {
            $users[] = [
                'name' => $faker->name(),
                'email' => $faker->email,
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'remember_token' => str_random(),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Illuminate\Database\Capsule\Manager::table('users')->insert($users);
    }
}
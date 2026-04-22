<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@erp.com'],
            [
                'name'     => 'Administrador',
                'password' => bcrypt('admin123'),
            ],
        );

        $this->call([
            ProdutoSeeder::class,
            CompraSeeder::class,
            VendaSeeder::class,
        ]);
    }
}

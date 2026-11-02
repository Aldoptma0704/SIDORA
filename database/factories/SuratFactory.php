<?php

namespace Database\Factories;

use App\Models\Surat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuratFactory extends Factory
{
    protected $model = Surat::class;

    public function definition()
    {
        return [
            'judul'   => $this->faker->sentence,
            'isi'     => $this->faker->paragraph,
            'user_id' => User::factory(),
        ];
    }
}

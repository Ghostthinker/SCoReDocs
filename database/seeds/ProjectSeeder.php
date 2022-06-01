<?php

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::truncate();

        for ($i = 1; $i <= 10; $i++) {
            Project::create([
                'title' => 'Title',
                'description' => 'admin@score'
            ]);
        }
    }
}

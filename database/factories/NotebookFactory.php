<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Notebook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notebook>
 */
class NotebookFactory extends Factory
{
    protected $model = Notebook::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Study Notes' => [
                'Mathematics', 'Physics', 'Computer Science', 'History', 'Literature',
                'Biology', 'Chemistry', 'Economics', 'Psychology'
            ],
            'Research Projects' => [
                'Market Analysis', 'Scientific Research', 'Data Analysis', 
                'Case Studies', 'Field Research'
            ],
            'Personal Development' => [
                'Goal Setting', 'Skill Learning', 'Habit Tracking',
                'Book Summaries', 'Course Notes'
            ],
            'Project Planning' => [
                'Business Ideas', 'Software Projects', 'Creative Projects',
                'Event Planning', 'Research Proposals'
            ]
        ];

        $category = fake()->randomElement(array_keys($categories));
        $subject = fake()->randomElement($categories[$category]);
        
        return [
            'title' => "{$category}: {$subject}",
            'description' => fake()->paragraph(3),
            'user_id' => User::factory()
        ];
    }
}

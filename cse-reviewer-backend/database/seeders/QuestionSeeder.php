<?php

namespace Database\Seeders;

use App\Models\PassageGroup;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class QuestionSeeder extends Seeder
{
    private array $categories = [
        'verbal' => 'verbal.json',
        'analytical' => 'analytical.json',
        'numerical' => 'numerical.json',
        'general_info' => 'general_info.json',
        'filipino' => 'filipino.json',
    ];

    public function run(): void
    {
        foreach ($this->categories as $category => $filename) {
            $this->seedCategory($category, $filename);
        }
    }

    private function seedCategory(string $category, string $filename): void
    {
        $path = database_path("seeders/data/{$filename}");

        if (!File::exists($path)) {
            $this->command->warn("File not found: {$filename}, skipping...");
            return;
        }

        $data = json_decode(File::get($path), true);

        if (!$data) {
            $this->command->error("Invalid JSON in: {$filename}");
            return;
        }

        // Seed passage groups first (questions reference them via FK)
        $passageGroups = $data['passageGroups'] ?? [];
        foreach ($passageGroups as $group) {
            PassageGroup::updateOrCreate(
                ['id' => $group['id']],
                [
                    'category' => $group['category'],
                    'subcategory' => $group['subcategory'] ?? 'uncategorized',
                    'difficulty' => $group['difficulty'] ?? 'average',
                    'title' => $group['title'] ?? null,
                    'passage_text' => $group['passage'] ?? '',
                    'source' => $group['source'] ?? null,
                    'has_image' => false,
                ]
            );
        }

        // Seed standalone questions
        $questions = $data['questions'] ?? [];
        $count = 0;
        foreach ($questions as $q) {
            // Skip passage group questions — they'll be seeded from passage groups
            if (str_starts_with($q['id'], 'pg-')) {
                continue;
            }

            Question::updateOrCreate(
                ['id' => $q['id']],
                [
                    'category' => $q['category'] ?? $category,
                    'subcategory' => $q['subcategory'] ?? 'uncategorized',
                    'difficulty' => $q['difficulty'] ?? 'average',
                    'question_text' => $q['question'] ?? '',
                    'choices' => $q['choices'] ?? [],
                    'answer' => $q['answer'] ?? '',
                    'explanation' => $q['explanation'] ?? null,
                    'source' => $q['source'] ?? null,
                    'has_image' => $q['hasImage'] ?? false,
                    'passage_group_id' => null,
                    'version' => 1,
                ]
            );
            $count++;
        }

        // Seed passage group questions
        foreach ($passageGroups as $group) {
            $groupQuestions = $group['questions'] ?? [];
            foreach ($groupQuestions as $q) {
                Question::updateOrCreate(
                    ['id' => $q['id']],
                    [
                        'category' => $group['category'],
                        'subcategory' => $group['subcategory'] ?? 'uncategorized',
                        'difficulty' => $q['difficulty'] ?? $group['difficulty'] ?? 'average',
                        'question_text' => $q['question'] ?? '',
                        'choices' => $q['choices'] ?? [],
                        'answer' => $q['answer'] ?? '',
                        'explanation' => $q['explanation'] ?? null,
                        'source' => $group['source'] ?? null,
                        'has_image' => $q['hasImage'] ?? false,
                        'passage_group_id' => $group['id'],
                        'version' => 1,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("Seeded {$count} questions and " . count($passageGroups) . " passage groups from {$filename}");
    }
}
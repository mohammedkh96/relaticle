<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CreationSource;
use App\Models\Company;
use App\Models\Note;
use App\Models\People;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportLegacyDataSeeder extends Seeder
{
    private function safeString(?string $str, int $limit = 250): ?string
    {
        if (!$str)
            return null;
        $str = trim($str);
        // Ensure UTF-8
        $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        return mb_substr($str, 0, $limit);
    }

    private function logError(string $context, string $errorMessage, array $record): void
    {
        $logMessage = date('Y-m-d H:i:s') . " - {$context}: {$errorMessage}\n";
        // Optionally log record data if needed, but keep it brief or only on severe errors
        // $logMessage .= "Data: " . json_encode($record) . "\n"; 
        File::append(base_path('import_errors.log'), $logMessage);
    }

    public function run(): void
    {
        $jsonPath = base_path('invest_expo_data.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("File not found: {$jsonPath}");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        if (empty($data)) {
            $this->command->error("No data found in JSON file.");
            return;
        }

        $this->command->info("Found " . count($data) . " records. Starting import...");

        $user = User::first();
        if (!$user) {
            $this->command->error("No users found. Please seed users first.");
            return;
        }

        $teamId = $user->current_team_id ?? $user->teams()->first()?->id ?? \App\Models\Team::first()?->id;

        if (!$teamId) {
            $this->command->error("No team found. Please seed teams first.");
            return;
        }

        $bar = $this->command->getOutput()->createProgressBar(count($data));
        $bar->start();

        File::put(base_path('import_errors.log'), "Import Log Started: " . now() . "\n");

        foreach ($data as $record) {
            $companyName = $this->safeString($record['company_name'] ?? '');

            if (empty($companyName)) {
                $bar->advance();
                continue;
            }

            try {
                DB::transaction(function () use ($record, $companyName, $user, $teamId) {
                    // 1. Create/Find Company - TRUNCATED SAFE
                    $company = Company::firstOrCreate(
                        ['name' => $companyName],
                        [
                            'team_id' => $teamId,
                            'creator_id' => $user->id,
                            'creation_source' => CreationSource::IMPORT,
                            'address' => $this->safeString($record['address'] ?? null, 250),
                            'phone' => $this->safeString($record['company_tel'] ?? null, 50),
                        ]
                    );

                    if (empty($company->address) && !empty($record['address'])) {
                        $company->update(['address' => $this->safeString($record['address'], 250)]);
                    }
                    if (empty($company->phone) && !empty($record['company_tel'])) {
                        $company->update(['phone' => $this->safeString($record['company_tel'], 50)]);
                    }

                    // 2. Person
                    $person = null;
                    if (!empty($record['person_name'])) {
                        $personName = $this->safeString($record['person_name'], 150);
                        if ($personName) {
                            $person = People::firstOrCreate(
                                [
                                    'name' => $personName,
                                    'company_id' => $company->id
                                ],
                                [
                                    'team_id' => $teamId,
                                    'creator_id' => $user->id,
                                    'creation_source' => CreationSource::IMPORT,
                                ]
                            );
                        }
                    }

                    // 3. Notes
                    $contactNoteLines = [];
                    if (!empty($record['person_phone']))
                        $contactNoteLines[] = "Phone: " . $this->safeString($record['person_phone'], 100);
                    if (!empty($record['email']))
                        $contactNoteLines[] = "Email: " . $this->safeString($record['email'], 150);
                    if (!empty($record['note']))
                        $contactNoteLines[] = "Note: " . $this->safeString($record['note'], 500);

                    if (!empty($contactNoteLines)) {
                        $noteContent = implode("\n", $contactNoteLines);
                        $sourceLine = "Source: " . $this->safeString($record['source'] ?? 'Import', 100);
                        // Combine and truncate strictly to 250 chars as 'title' is VARCHAR(255)
                        $fullNote = $this->safeString("{$sourceLine}\n{$noteContent}", 250);

                        $target = $person ?? $company;

                        try {
                            // Check exact match on title since we truncated
                            $exists = $target->notes()
                                ->where('title', $fullNote)
                                ->exists();

                            if (!$exists) {
                                $target->notes()->create([
                                    'title' => $fullNote,
                                    'team_id' => $teamId,
                                    'creator_id' => $user->id,
                                    'creation_source' => CreationSource::IMPORT,
                                ]);
                            }
                        } catch (\Exception $e) {
                            $this->logError("Failed to add note for {$companyName}", $e->getMessage(), $record);
                        }
                    }

                    // 4. Events
                    if (!empty($record['is_exhibitor'])) {
                        $sourceClean = str_replace(['Excel: ', 'CSV: '], '', $record['source'] ?? '');
                        $eventName = $this->safeString($sourceClean, 150);

                        preg_match('/\b(20\d{2})\b/', $eventName, $matches);
                        $year = $matches[1] ?? '2024';

                        $event = \App\Models\Event::firstOrCreate(
                            ['name' => $eventName],
                            [
                                'year' => $year,
                                'status' => \App\Enums\EventStatus::UPCOMING,
                                'start_date' => "$year-01-01",
                                'end_date' => "$year-12-31",
                            ]
                        );

                        if (!\App\Models\Participation::where('company_id', $company->id)->where('event_id', $event->id)->exists()) {
                            \App\Models\Participation::create([
                                'company_id' => $company->id,
                                'event_id' => $event->id,
                                'participation_status' => \App\Enums\ParticipationStatus::CONFIRMED,
                                'notes' => "Imported from " . $this->safeString($record['source'] ?? '', 50),
                            ]);
                        }
                    }

                    // 5. Opportunities
                    // Fallback: If logic
                    if (!empty($record['is_employee_entry']) || empty($record['is_exhibitor'])) {
                        $oppName = "Lead: " . $companyName;
                        if (!empty($personName)) {
                            $oppName .= " - " . $personName;
                        }

                        if (!\App\Models\Opportunity::where('company_id', $company->id)->where('name', $oppName)->exists()) {
                            \App\Models\Opportunity::create([
                                'name' => $oppName,
                                'company_id' => $company->id,
                                'contact_id' => $person?->id,
                                'team_id' => $teamId,
                                'creator_id' => $user->id,
                                'creation_source' => CreationSource::IMPORT,
                                'status' => \App\Enums\OpportunityStatus::New ,
                                'temperature' => \App\Enums\OpportunityTemperature::Cold,
                            ]);
                        }
                    }

                });
            } catch (\Exception $e) {
                File::append(base_path('import_errors.log'), "Failed {$companyName}: " . $e->getMessage() . "\n");
                $this->command->error("Err: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Import completed. See import_errors.log for details.");
    }
}

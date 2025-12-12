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

        // Get default user and team (Admin)
        $user = User::first();
        if (!$user) {
            $this->command->error("No users found. Please seed users first.");
            return;
        }

        // Assuming user has a team or we can get the first team
        $teamId = $user->current_team_id ?? $user->teams()->first()?->id ?? \App\Models\Team::first()?->id;

        if (!$teamId) {
            $this->command->error("No team found. Please seed teams first.");
            return;
        }

        $bar = $this->command->getOutput()->createProgressBar(count($data));
        $bar->start();

        foreach ($data as $record) {
            $companyName = trim($record['company_name'] ?? '');
            if (empty($companyName)) {
                $bar->advance();
                continue;
            }

            try {
                DB::transaction(function () use ($record, $companyName, $user, $teamId) {
                    // 1. Create/Find Company
                    $company = Company::firstOrCreate(
                        ['name' => $companyName],
                        [
                            'team_id' => $teamId,
                            'creator_id' => $user->id,
                            'creation_source' => CreationSource::IMPORT,
                            'address' => $record['address'] ?? null,
                            'phone' => $record['company_tel'] ?? null,
                        ]
                    );

                    // Update phone/address if empty and we have new data
                    if (empty($company->address) && !empty($record['address'])) {
                        $company->update(['address' => $record['address']]);
                    }
                    if (empty($company->phone) && !empty($record['company_tel'])) {
                        $company->update(['phone' => $record['company_tel']]);
                    }

                    // 2. Create/Find Person
                    $person = null;
                    if (!empty($record['person_name'])) {
                        $person = People::firstOrCreate(
                            [
                                'name' => trim($record['person_name']),
                                'company_id' => $company->id
                            ],
                            [
                                'team_id' => $teamId,
                                'creator_id' => $user->id,
                                'creation_source' => CreationSource::IMPORT,
                            ]
                        );
                    }

                    // 3. Add Contact Info as Notes (since we can't change DB schema)
                    $contactNoteLines = [];
                    if (!empty($record['person_phone'])) {
                        $contactNoteLines[] = "Phone: " . $record['person_phone'];
                    }
                    if (!empty($record['email'])) {
                        $contactNoteLines[] = "Email: " . $record['email'];
                    }
                    if (!empty($record['note'])) {
                        $contactNoteLines[] = "Note: " . $record['note'];
                    }

                    if (!empty($contactNoteLines)) {
                        $noteContent = implode("\n", $contactNoteLines);
                        $sourceLine = "Source: " . ($record['source'] ?? 'Import');
                        $fullNote = "{$sourceLine}\n{$noteContent}";

                        // Attach note to Person if exists, otherwise Company
                        $target = $person ?? $company;

                        // Check if duplicate note exists to avoid spamming runs
                        // Simple check: same content
                        $exists = $target->notes()
                            ->where('content', 'LIKE', "%{$noteContent}%")
                            ->exists();

                        if (!$exists) {
                            $target->notes()->create([
                                'content' => $fullNote,
                                'team_id' => $teamId,
                                'creator_id' => $user->id,
                                'creation_source' => CreationSource::IMPORT,
                            ]);
                        }
                    }
                    // 4. Link to Event (Create Participation)
                    // Extract year from source string (e.g., "CSV: 2019", "Excel: Invest Expo 2019")
                    $source = $record['source'] ?? '';
                    preg_match('/\b(20\d{2})\b/', $source, $matches);
                    $year = $matches[1] ?? null;

                    if ($year) {
                        $eventName = "Invest Expo $year";

                        // Find or Create Event
                        $event = \App\Models\Event::firstOrCreate(
                            ['year' => $year],
                            [
                                'name' => $eventName,
                                'status' => \App\Enums\EventStatus::UPCOMING, // Default status
                                'start_date' => "$year-01-01", // Placeholder dates
                                'end_date' => "$year-12-31",
                            ]
                        );

                        // Check if participation already exists
                        $participationExists = \App\Models\Participation::where('company_id', $company->id)
                            ->where('event_id', $event->id)
                            ->exists();

                        if (!$participationExists) {
                            \App\Models\Participation::create([
                                'company_id' => $company->id,
                                'event_id' => $event->id,
                                'participation_status' => \App\Enums\ParticipationStatus::CONFIRMED, // Assume they participated
                                'notes' => "Imported from $source",
                            ]);
                        }
                    }

                    // 5. Create Opportunity (if marked as such)
                    if (!empty($record['is_opportunity'])) {
                        $oppName = "Business Card Import: " . ($companyName ?? $record['person_name'] ?? 'Unknown');

                        // Prevent duplicates
                        $oppExists = \App\Models\Opportunity::where('company_id', $company->id)
                            ->where('name', $oppName)
                            ->exists();

                        if (!$oppExists) {
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
                // Log checking
                // $this->command->error("Failed to import {$companyName}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Import completed.");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddRedirectToEditPages extends Command
{
    protected $signature = 'fix:add-redirects';
    protected $description = 'Add getRedirectUrl method to all Edit pages';

    public function handle()
    {
        $editPages = [
            'app-modules/SystemAdmin/src/Filament/Resources/PeopleResource/Pages/EditPeople.php',
            'app-modules/SystemAdmin/src/Filament/Resources/VisitorResource/Pages/EditVisitor.php',
            'app-modules/SystemAdmin/src/Filament/Resources/ParticipationResource/Pages/EditParticipation.php',
            'app-modules/SystemAdmin/src/Filament/Resources/OpportunityResource/Pages/EditOpportunity.php',
            'app-modules/SystemAdmin/src/Filament/Resources/TaskResource/Pages/EditTask.php',
            'app-modules/SystemAdmin/src/Filament/Resources/TeamResource/Pages/EditTeam.php',
            'app-modules/SystemAdmin/src/Filament/Resources/UserResource/Pages/EditUser.php',
            'app-modules/SystemAdmin/src/Filament/Resources/NoteResource/Pages/EditNote.php',
            'app-modules/SystemAdmin/src/Filament/Resources/SystemAdministrators/Pages/EditSystemAdministrator.php',
        ];

        $redirectMethod = "
    protected function getRedirectUrl(): string
    {
        return \$this->getResource()::getUrl('index');
    }";

        foreach ($editPages as $file) {
            $path = base_path($file);

            if (!File::exists($path)) {
                $this->warn("File not found: $file");
                continue;
            }

            $content = File::get($path);

            // Check if already has redirect
            if (str_contains($content, 'getRedirectUrl')) {
                $this->info("✓ Already has redirect: $file");
                continue;
            }

            // Add redirect before closing brace
            $content = preg_replace('/(\n}\s*)$/', $redirectMethod . '$1', $content);

            File::put($path, $content);
            $this->info("✓ Added redirect to: $file");
        }

        $this->info("\n✅ All Edit pages updated!");
    }
}

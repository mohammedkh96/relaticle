<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default communication settings
        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'mail_mailer',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'mail_host',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'mail_port',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'mail_username',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'mail_password',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'mail_encryption',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'mail_from_address',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'mail_from_name',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'whatsapp_api_url',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'whatsapp_api_token',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('settings')->insert([
            'group' => 'communication',
            'name' => 'whatsapp_phone_number_id',
            'locked' => false,
            'payload' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('settings')->where('group', 'communication')->delete();
    }
};

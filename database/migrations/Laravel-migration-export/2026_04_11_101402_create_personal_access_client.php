<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePersonalAccessClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si un client personnel existe déjà
        $existingClient = DB::table('oauth_clients')->where('personal_access_client', 1)->first();

        if (!$existingClient) {
            DB::table('oauth_clients')->insert([
                'id' => 3, // ID différent des clients existants
                'user_id' => null,
                'name' => 'Personal Access Client',
                'secret' => 'T9bg4X0zABhISa567GLwfKsK0nBGfseSpcJgUwGo', // Même secret que le client normal pour simplicité
                'provider' => null,
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('oauth_clients')->where('personal_access_client', 1)->delete();
    }
}

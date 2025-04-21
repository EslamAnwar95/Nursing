<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\DB;
class PassportPasswordClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ public function run(): void
    {
        $clientRepo = app(ClientRepository::class);

        // 1. مريض
        $existingPatient = DB::table('oauth_clients')->where('provider', 'patients')->where('password_client', true)->first();
        if (!$existingPatient) {
            $patientClient = $clientRepo->createPasswordGrantClient(
                null, // user_id (null for password clients)
                'Patient Password Grant Client',
                config('app.url') . '/callback',
                'patients'
            );

       
        }

        // 2. ممرض
        $existingNurse = DB::table('oauth_clients')->where('provider', 'nurses')->where('password_client', true)->first();
        if (!$existingNurse) {
            $nurseClient = $clientRepo->createPasswordGrantClient(
                null,
                'Nurse Password Grant Client',
                config('app.url') . '/callback',
                'nurses'
            );

         
        }
    }
}

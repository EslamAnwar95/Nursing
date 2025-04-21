<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

trait HandlesPassportToken
{

    public function issueAccessToken(string $email, string $password, string $provider): array
    {
     
        $client = DB::table('oauth_clients')
            ->where('password_client', true)
            ->where('provider', $provider)
            ->latest('created_at')
            ->first();

        if (!$client) {
            return [
                'success' => false,
                'status' => 500,
                'message' => "OAuth client not found for provider [$provider]."
            ];
        }

        $response = Http::asForm()->post(url('/api/oauth/token'), [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ]);

        if ($response->failed()) {
            logger()->error('Token generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            return [
                'success' => false,
                'status' => $response->status(),
                'message' => 'Token generation failed.',
                'errors' => $response->json(),
            ];
        }

        return [
            'success' => true,
            'status' => 200,
            'token' => $response->json(),
        ];
    }
}

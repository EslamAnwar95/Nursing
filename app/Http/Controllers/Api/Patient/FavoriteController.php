<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\Patient\FavoriteResource;
use App\Models\Favorite;
use App\Models\Nurse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{


    public function addFavorite(Request $request)
    {

        $patient = auth('patient')->user();


        $request->validate([
            'provider_id' => 'required|exists:nurses,id',
            'provider_type' => 'required|in:nurse,blood_bank,lab',
        ]);

        $providerType = match ($request->provider_type) {
            'nurse' => Nurse::class,
            // 'lab' => Lab::class,
            // 'blood_bank' => BloodBank::class,
        };

        Favorite::firstOrCreate([
            'patient_id' => $patient->id,
            'provider_id' => $request->provider_id,
            'provider_type' => $providerType,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Favorite added successfully',

        ]);
    }


    public function removeFavorite(Request $request)
    {
        $patient = auth('patient')->user();

        $request->validate([
            'provider_id' => 'required|exists:nurses,id',
            'provider_type' => 'required|in:nurse,blood_bank,lab',
        ]);
        
        $providerType = match ($request->provider_type) {
            'nurse' => Nurse::class,
            // 'lab' => Lab::class,
            // 'blood_bank' => BloodBank::class,
        };

        Favorite::where([
            'patient_id' => $patient->id,
            'provider_id' => $request->provider_id,
            'provider_type' => $providerType,
        ])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Favorite removed successfully',
        ]);
    }


    public function myFavorites()
    {
        $patient = auth('patient')->user();
    
        $favorites = Favorite::where('patient_id', $patient->id)->with('provider')->paginate(10);
        
        return FavoriteResource::collection($favorites)->additional([
            'status' => true,
            'message' => __('messages.favorites_list'),
        ]);
        return response()->json([
            'status' => true,
            'data' => $favorites->map(function ($fav) {
                return [
                    'id' => $fav->provider->id,
                    'type' => class_basename($fav->provider_type),
                    'name' => $fav->provider->full_name ?? null,
                    'phone' => $fav->provider->phone_number ?? null,
                    'image' => $fav->provider->profile_image_url ?? null,
                ];
            }),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Nurse\Auth\NurseRegisterRequest;
use App\Models\Nurse;
use App\Traits\HandlesPassportToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    use HandlesPassportToken;
    public function register(NurseRegisterRequest $request)
    {
      
        DB::beginTransaction();

        try {
            // 1. إنشاء الممرض
            $nurse = Nurse::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'union_card_number' => $request->union_card_number,
                'password' => Hash::make($request->password),
            ]);
       
          
            if ($request->hasFile('profile_image')) {
                $nurse->addMedia($request->file('profile_image'))->toMediaCollection('profile_image');
            }
            if ($request->hasFile('id_card_front')) {
                $nurse->addMedia($request->file('id_card_front'))->toMediaCollection('id_card_front');
            }
            if ($request->hasFile('id_card_back')) {
                $nurse->addMedia($request->file('id_card_back'))->toMediaCollection('id_card_back');
            }
            if ($request->hasFile('union_card_back')) {
                $nurse->addMedia($request->file('union_card_back'))->toMediaCollection('union_card_back');
            }
            if ($request->hasFile('criminal_record')) {
                $nurse->addMedia($request->file('criminal_record'))->toMediaCollection('criminal_record');
            }
        
          
            DB::commit();
      
            $result = $this->issueAccessToken($request->email, $request->password, 'nurses');

            if (!$result['success']) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null,
                ], $result['status']);
            }

            return response()->json([
                'status' => true,
                'message' => 'Nurse registered and logged in successfully.',
                'data' => [
                    'token' => $result['token'],
                    'nurse' => $nurse,
                ]
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء التسجيل.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
}

<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Nurse\Auth\NurseRegisterRequest;
use App\Models\Nurse;
use App\Traits\HandlesPassportToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Nurse\Auth\NurseLoginRequest;
use App\Mail\NurseOtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;



class AuthController extends Controller
{
    use HandlesPassportToken;
    public function register(NurseRegisterRequest $request)
    {

        DB::beginTransaction();

        try {
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


    public function login(NurseLoginRequest $request)
    {
        try {
            $nurse = Nurse::where('email', $request->email)->first();

            if (!$nurse || !Hash::check($request->password, $nurse->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'
                ], 401);
            }

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
                'message' => 'Login successful.',
                'data' => [
                    'token' => $result['token'],
                    'nurse' => $nurse,
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدخول.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nurses,email'
        ]);

        $otp = rand(1000, 9999);

        Otp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($request->email)->send(new NurseOtpMail($otp));

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nurses,email',
            'otp' => 'required|digits:4'
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json([
                'status' => false,
                'message' => 'رمز التحقق غير صالح أو منتهي.'
            ], 422);
        }

        $otp->update(['verified_at' => now()]);


        return response()->json([
            'status' => true,
            'message' => 'تم التحقق بنجاح.',
         
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nurses,email',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);


        // تحديث كلمة المرور
        $nurse = Nurse::where('email', $request->email)->first();
        $nurse->update([
            'password' => Hash::make($request->password)
        ]);

    

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح.'
        ]);
    }
}

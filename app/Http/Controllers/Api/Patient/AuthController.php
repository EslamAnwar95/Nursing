<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Patient\Auth\PatientRegisterRequest;
use App\Http\Requests\Patient\Auth\PatientLoginRequest;
use App\Models\Patient;
use App\Traits\HandlesPassportToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Mail\PatientOtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\Patient\PatientInfoResource;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{


    use HandlesPassportToken;

    public function register(PatientRegisterRequest $request)
    {
        try {

            DB::beginTransaction();

            // Step 1: Create the patient
            $paitent = Patient::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);


            if ($request->hasFile('patient_avatar')) {
                $paitent->addMedia($request->file('patient_avatar'))->toMediaCollection('patient_avatar');
            }

            DB::commit();

            $result = $this->issueAccessToken($request->email, $request->password, 'patients');

            
            if (!$result['success']) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null,
                ], $result['status']);
            }

            return response()->json([
                'status' => 201,
                'message' => 'Candidate registered and logged in successfully.',
                'data' => [
                    'token' => $result['token']
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
           
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during registration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login(PatientLoginRequest $request)
    {
        try {
            $patient = Patient::where('email', $request->email)->first();

            if (!$patient || !Hash::check($request->password, $patient->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'
                ], 401);
            }
            $result = $this->issueAccessToken($request->email, $request->password, 'patients');

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
                    'patient' => PatientInfoResource::make($patient),
                ]
            ], 200);
        } catch (\Exception $e) {


            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during login.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:patients,email'
        ]);

        $otp = rand(1000, 9999); // أو use OTP service
        $email = $request->email;




        Otp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Send email or SMS here (Queue preferred)
        Mail::to($email)->send(new PatientOtpMail($otp));


        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:patients,email',
            'otp' => 'required|digits:4'
        ]);

        $otpRecord = Otp::where('email', $request->email)
            ->where('otp_code', $request->otp)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.'
            ], 422);
        }

        // ✅ Mark OTP as verified
        $otpRecord->update([
            'verified_at' => now()
        ]);



        return response()->json([
            'status' => 200,
            'message' => 'تم التحقق من الرمز بنجاح.',

        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:patients,email',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);


        // تحديث كلمة المرور
        $patient = Patient::where('email', $request->email)->first();
        $patient->update([
            'password' => Hash::make($request->password)
        ]);

    

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح.'
        ]);
    }

    public function profile(Request $request)
    {
        // Return authenticated patient's profile
    }
}

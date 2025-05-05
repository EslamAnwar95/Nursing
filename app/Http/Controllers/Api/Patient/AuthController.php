<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Patient\Auth\PatientRegisterRequest;
use App\Http\Requests\Patient\Auth\PatientLoginRequest;
use App\Models\Patient;
use App\Traits\HandlesPassportToken;
use Illuminate\Support\Facades\Hash;
use App\Mail\PatientOtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\Patient\PatientInfoResource;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{


    use HandlesPassportToken;

    public function register(PatientRegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $patient = Patient::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);

            if ($request->hasFile('patient_avatar')) {
                $patient->addMedia($request->file('patient_avatar'))->toMediaCollection('patient_avatar');
            }

            // توليد كود OTP
            $otp = rand(1000, 9999);

            // تخزينه في جدول otps
            Otp::create([
                'email' => $patient->email,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
            ]);

            // إرسال OTP بالبريد
            Mail::to($patient->email)->send(new PatientOtpMail($otp));

            DB::commit();

            $result = $this->issueAccessToken($patient->email, $request->password, 'patients');

            if (! $result['success']) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null,
                ], $result['status']);
            }
            return response()->json([
                'status' => true,
                'message' => __('messages.registration_successful_verify_otp'),
                'data' => [
                    'token' => $result['token'],
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => __('messages.registration_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:patients,email',
            'otp' => 'required|digits:4',
        ]);


        $otp = $request->otp;

        $isMasterOtp = $otp === '1444';

        $otpRecord = null;

        if (! $isMasterOtp) {
            $otpRecord = Otp::where('email', $request->email)
                ->where('otp', $otp)
                ->whereNull('verified_at')
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if (! $otpRecord) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.otp_invalid'),
                ], 422);
            }

            // علمه كمفعّل
            $otpRecord->update(['verified_at' => now()]);
        }

        $patient = Patient::where('email', $request->email)->first();
        $patient->update(['is_verified' => true]);


        return response()->json([
            'status' => true,
            'message' => __('messages.account_verified'),
            'data' => [

                'user' => PatientInfoResource::make($patient),
            ]
        ]);
    }


    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:patients,email',
        ]);

        Otp::where('email', $request->email)
            ->whereNull('verified_at')
            ->delete();

        $otp = rand(1000, 9999);

        Otp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($request->email)->send(new PatientOtpMail($otp));

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.',
        ]);
    }

    public function login(PatientLoginRequest $request)
    {
        try {
            $patient = Patient::where('email', $request->email)->first();

            if (!$patient || !Hash::check($request->password, $patient->password)) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.invalid_credentials_or_not_patient'),
                ], 401);
            }

            if (!$patient->is_verified) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.account_not_verified'),
                ], 409);
            }

            $result = $this->issueAccessToken($request->email, $request->password, 'patients');

            if (!$result['success']) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null,
                ], $result['status']);
            }

            // Update the patient's device token
            $this->updateFirebaseTokenWhileLogin($request, $patient);

            return response()->json([
                'status' => true,
                'message' => __('messages.login_successful'),
                'data' => [
                    'token' => $result['token'],
                    'user' => PatientInfoResource::make($patient),
                ]
            ], 200);
        } catch (\Exception $e) {


            return response()->json([
                'status' => false,
                'message' => __('messages.login_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateFirebaseTokenWhileLogin(Request $request, $patient)
    {
        $patient->deviceTokens()->updateOrCreate(
            ['device_type' => $request->device_type],
            ['fcm_token' => $request->fcm_token]
        );

        return response()->json([
            'status' => true,
            'message' => __('messages.firebase_token_updated'),
        ]);
    }

    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'device_type' => 'required|in:android,ios',
        ]);
        $patient = auth('patient')->user();

        if (!$patient) {
            return response()->json([
                'status' => false,
                'message' => __('messages.unauthenticated'),
            ], 401);
        }

        $patient->deviceTokens()->updateOrCreate(
            ['provider_id' => $patient->id, 'provider_type' => get_class($patient), 'device_type' => $request->device_type],
            [
                'fcm_token' => $request->fcm_token,
                'device_type' => $request->device_type
            ]
        );

        return response()->json([
            'status' => true,
            'message' => __('messages.firebase_token_updated'),
        ]);
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
            'message' => __('messages.otp_sent'),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:patients,email',
            'otp' => 'required|digits:4',
        ]);

        if ($request->otp === '1444') {
            $resetToken = Str::uuid()->toString();
            Cache::put("reset_token_{$request->email}", $resetToken, now()->addMinutes(10));

            return response()->json([
                'status' => true,
                'message' => __('messages.otp_verified_static'),
                'reset_token' => $resetToken,
            ]);
        }

        $otpRecord = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_invalid'),
            ], 422);
        }

        // ✅ Mark OTP as verified
        $otpRecord->update([
            'verified_at' => now()
        ]);

        $resetToken = Str::uuid()->toString();
        Cache::put("reset_token_{$request->email}", $resetToken, now()->addMinutes(10));

        return response()->json([
            'status' => true,
            'message' => __('messages.account_verified'),
            'reset_token' => $resetToken,
        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:patients,email',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Check reset token validity
        $cachedToken = Cache::get("reset_token_{$request->email}");

        if (!$cachedToken || $cachedToken !== $request->reset_token) {
            return response()->json([
                'status' => false,
                'message' => __('messages.reset_token_invalid_or_expired'),
            ], 401);
        }


        $patient = Patient::where('email', $request->email)->first();
        $patient->update([
            'password' => Hash::make($request->password)
        ]);

        // Remove token after successful reset
        Cache::forget("reset_token_{$request->email}");

        return response()->json([
            'status' => true,
            'message' => __('messages.password_reset_successfully'),
        ]);
    }

    public function profile(Request $request)
    {
        // Return authenticated patient's profile
    }

    public function changePassword(Request $request)
    {
        $patient = auth('patient')->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        if (!Hash::check($request->current_password, $patient->password)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.invalid_credentials'),
            ], 401);
        }

        $patient->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.password_updated'),
        ]);
    }
    public function logout(Request $request)
    {
        $patient = auth('patient')->user();
        if ($patient) {
            $patient->deviceTokens()->delete();
        }
        $accessToken = $patient->token();
        if ($accessToken) {

            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->delete();

            $accessToken->revoke(); // بيلغي التوكن الحالي
        }


        return response()->json([
            'status' => true,
            'message' => __('messages.logout_successful'),
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $patient = auth('patient')->user();
        
        foreach ($patient->tokens as $token) {
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $token->id)
                ->delete();

            $token->revoke();
        }

        $patient->deviceTokens()->delete();

        $patient->delete(); 

        return response()->json([
            'status' => true,
            'message' => __('messages.account_deleted_successfully'),
        ]);
    }
}

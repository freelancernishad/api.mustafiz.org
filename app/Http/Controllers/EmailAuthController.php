<?php

namespace App\Http\Controllers;

use App\Models\OtpVerification;
use App\Mail\OtpMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailAuthController extends Controller
{
    /**
     * Send OTP to the user's email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Save OTP details in otp_verifications table
        OtpVerification::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'otp_expires_at' => Carbon::now()->addMinutes(10), 'verified' => false]
        );

        // Send OTP email
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP sent to your email.']);
    }

    /**
     * Verify OTP for email verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Find the OTP verification record
        $otpVerification = OtpVerification::where('email', $request->email)
                            ->where('otp', $request->otp)
                            ->where('otp_expires_at', '>', Carbon::now())
                            ->where('verified', false)
                            ->first();

        if (!$otpVerification) {
            return response()->json(['error' => 'Invalid OTP or OTP expired.'], 400);
        }

        // Mark OTP as verified
        $otpVerification->verified = true;
        $otpVerification->save();

        return response()->json(['message' => 'OTP verified successfully.']);
    }

    /**
     * Send verification link with OTP to the user's email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationLink(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Save OTP details in otp_verifications table
        OtpVerification::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'otp_expires_at' => Carbon::now()->addMinutes(10), 'verified' => false]
        );

        // Send OTP email
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'Verification link sent to your email.']);
    }

    /**
     * Verify email using OTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Find the OTP verification record
        $otpVerification = OtpVerification::where('email', $request->email)
                            ->where('otp', $request->otp)
                            ->where('otp_expires_at', '>', Carbon::now())
                            ->where('verified', false)
                            ->first();

        if (!$otpVerification) {
            return response()->json(['error' => 'Invalid OTP or OTP expired.'], 400);
        }

        // Mark OTP verification as complete
        $otpVerification->verified = true;
        $otpVerification->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }
}

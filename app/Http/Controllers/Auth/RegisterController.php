<?php 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use App\Notifications\SendOtpNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    // Show registration form
    public function showRegistrationForm()
    {
        // $user = User::first();
        // $user->notify(new SendOtpNotification);
        return view('auth.register');
    }

    // Handle user registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create a new user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Generate and store OTP
        $otp = Otp::create([
            'user_id' => $user->id,
            'otp' => rand(1000, 9999),
            'expire_at' => now()->addMinutes(5),
        ]);

        // Implement logic to send OTP via SMS gateway
        $user->notify(new SendOtpNotification($otp->otp));
        return redirect()->route('verify-otp', ['user_id' => $user->id]);
    }

    // Show OTP verification form
    public function showOtpVerificationForm(Request $request)
    {
        return view('auth.verify_otp', ['user_id' => $request->input('user_id')]);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required',
        ]);

        $user = User::find($request->input('user_id'));

        if ($user && $this->isValidOtp($user, $request->input('otp'))) {
            Auth::login($user);
            return redirect('/login')->with('success', 'OTP verified successfully. You can now log in.');
        }

        return redirect()->back()->with('error', 'Invalid OTP. Please try again.');
    }

    // Validation logic for OTP
    protected function isValidOtp(User $user, $otp)
    {
        return $user->otps()
            ->where('otp', $otp)
            ->where('expire_at', '>', now())
            ->exists();
    }
}

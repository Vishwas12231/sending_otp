<!-- In verify_otp.blade.php -->

<form method="post" action="{{ route('verify-otp') }}">
    @csrf

    <input type="hidden" name="user_id" value="{{ $user_id }}">
    <input type="text" name="otp" placeholder="Enter OTP" required>

    <button type="submit">Verify OTP</button>
</form>

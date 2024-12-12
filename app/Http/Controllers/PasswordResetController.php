<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class PasswordResetController extends Controller
{
     
    public function forgotPassword(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    if ($status === Password::RESET_LINK_SENT) {
        // Create the reset URL with the desired frontend URL
        $resetUrl = 'http://localhost:3000/reset-password?token=' . urlencode($status) . '&email=' . urlencode($request->input('email'));
        
        // Send the email with the reset URL
        Mail::to($request->input('email'))->send(new PasswordResetMail($resetUrl));

        return response()->json(['message' => 'Password reset link sent to your email']);
    } else {
        return response()->json(['message' => 'Error sending reset link'], 500);
    }
}

public function resetPassword(Request $request)
{
    // Validate the request data
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    // Find the user by the provided email
    $user = User::where('email', $request->input('email'))->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Update the password
    $user->password = Hash::make($request->input('password'));
    $user->save();

    return response()->json(['message' => 'Password reset successfully']);
}
}

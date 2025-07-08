<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SecurityQuestionResetController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    public function handleEmailForm(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->securityQuestions) {
            return back()->withErrors(['email' => 'No account found with that email or security questions are not set up.']);
        }

        $request->session()->put('password_reset_user_id', $user->id);

        return redirect()->route('password.questions');
    }

    public function showQuestionForm(Request $request)
    {
        if (!$request->session()->has('password_reset_user_id')) {
            return redirect()->route('password.request');
        }

        $user = User::find($request->session()->get('password_reset_user_id'));
        $questions = $user->securityQuestions;

        return view('auth.reset-password-questions', ['question1' => $questions->question_1, 'question2' => $questions->question_2]);
    }

    public function verifyQuestions(Request $request)
    {
        $request->validate([
            'answer_1' => 'required|string',
            'answer_2' => 'required|string',
        ]);

        if (!$request->session()->has('password_reset_user_id')) {
            return redirect()->route('password.request');
        }

        $user = User::find($request->session()->get('password_reset_user_id'));
        $securityQuestions = $user->securityQuestions;

        $answer1Correct = Hash::check(strtolower(trim($request->answer_1)), $securityQuestions->answer_1);
        $answer2Correct = Hash::check(strtolower(trim($request->answer_2)), $securityQuestions->answer_2);

        if ($answer1Correct && $answer2Correct) {
            $request->session()->put('password_reset_verified', true);
            return redirect()->route('password.reset');
        }

        return back()->withErrors(['answers' => 'One or both answers are incorrect.']);
    }

    public function showNewPasswordForm(Request $request)
    {
        if (!$request->session()->has('password_reset_verified')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password');
    }

    public function updatePassword(Request $request)
    {
        if (!$request->session()->has('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::find($request->session()->get('password_reset_user_id'));
        $user->password = Hash::make($request->password);
        $user->save();

        $request->session()->forget(['password_reset_user_id', 'password_reset_verified']);

        return redirect('/login')->with('status', 'Your password has been reset successfully!');
    }
}
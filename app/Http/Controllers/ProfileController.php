<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\SecurityQuestion;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function storeSecurityQuestions(Request $request): RedirectResponse
    {
        $request->validate([
            'question_1' => 'required|string|different:question_2',
            'answer_1' => 'required|string|min:3',
            'question_2' => 'required|string|different:question_1',
            'answer_2' => 'required|string|min:3',
        ]);

        SecurityQuestion::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'question_1' => $request->question_1,
                'answer_1' => $request->answer_1,
                'question_2' => $request->question_2,
                'answer_2' => $request->answer_2,
            ]
        );

        return Redirect::route('profile.edit')->with('status', 'security-questions-saved');
    }
}

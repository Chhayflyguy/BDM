<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- THIS IS THE NEW SECTION TO ADD --}}
        <div class="mt-6 pt-6 border-t">
            <h3 class="text-lg font-medium text-gray-900">Security Questions</h3>
            <p class="mt-1 text-sm text-gray-600">
                Please set up two security questions. These will be used to recover your account if you forget your password.
            </p>

            @php
                $questions = [
                    "What was your first pet's name?",
                    "What is your mother's maiden name?",
                    "What was the name of your elementary school?",
                    "In what city were you born?",
                    "What is your favorite movie?",
                ];
            @endphp
            
            <div class="mt-4">
                <x-input-label for="question_1" :value="__('Question 1')" />
                <select name="question_1" id="question_1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @foreach($questions as $question)
                        <option value="{{ $question }}" @selected(old('question_1') == $question)>{{ $question }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4">
                <x-input-label for="answer_1" :value="__('Answer 1')" />
                <x-text-input id="answer_1" name="answer_1" type="text" class="mt-1 block w-full" :value="old('answer_1')" required />
                <x-input-error :messages="$errors->get('answer_1')" class="mt-2" />
            </div>
            
            <div class="mt-4">
                <x-input-label for="question_2" :value="__('Question 2')" />
                <select name="question_2" id="question_2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                     @foreach($questions as $question)
                        <option value="{{ $question }}" @selected(old('question_2') == $question)>{{ $question }}</option>
                    @endforeach
                </select>
                 <x-input-error :messages="$errors->get('question_2')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="answer_2" :value="__('Answer 2')" />
                <x-text-input id="answer_2" name="answer_2" type="text" class="mt-1 block w-full" :value="old('answer_2')" required />
                <x-input-error :messages="$errors->get('answer_2')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

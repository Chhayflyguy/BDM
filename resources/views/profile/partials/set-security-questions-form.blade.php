<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ Auth::user()->securityQuestions ? __('Update Security Questions') : __('Set Security Questions') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Set up security questions to recover your account if you forget your password.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.security-questions.store') }}" class="mt-6 space-y-6">
        @csrf
        
        @php
            $questions = [
                "What was your first pet's name?",
                "What is your mother's maiden name?",
                "What was the name of your elementary school?",
                "In what city were you born?",
                "What is your favorite movie?",
            ];
        @endphp

        <div>
            <x-input-label for="question_1" :value="__('Question 1')" />
            <select name="question_1" id="question_1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @foreach($questions as $question)
                    <option value="{{ $question }}" @selected(Auth::user()->securityQuestions?->question_1 == $question)>{{ $question }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="answer_1" :value="__('Answer 1')" />
            <x-text-input id="answer_1" name="answer_1" type="text" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('answer_1')" class="mt-2" />
        </div>
        <hr class="my-4">
        <div>
            <x-input-label for="question_2" :value="__('Question 2')" />
            <select name="question_2" id="question_2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                 @foreach($questions as $question)
                    <option value="{{ $question }}" @selected(Auth::user()->securityQuestions?->question_2 == $question)>{{ $question }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="answer_2" :value="__('Answer 2')" />
            <x-text-input id="answer_2" name="answer_2" type="text" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('answer_2')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'security-questions-saved')
                <p class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
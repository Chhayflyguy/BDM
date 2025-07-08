<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please answer your security questions to verify your identity.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.questions.verify') }}">
        @csrf

        <!-- Question 1 -->
        <div class="mt-4">
            <x-input-label for="answer_1" :value="__($question1)" />
            <x-text-input id="answer_1" class="block mt-1 w-full" type="text" name="answer_1" required autofocus />
        </div>
        
        <!-- Question 2 -->
        <div class="mt-4">
            <x-input-label for="answer_2" :value="__($question2)" />
            <x-text-input id="answer_2" class="block mt-1 w-full" type="text" name="answer_2" required />
        </div>

        <x-input-error :messages="$errors->get('answers')" class="mt-2" />

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verify Answers') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
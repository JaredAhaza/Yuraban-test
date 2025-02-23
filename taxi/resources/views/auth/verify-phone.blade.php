<x-guest-layout>
    <form method="POST" action="{{ route('verification.phone') }}">
        @csrf

        <div>
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" name="phone" type="text" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="verification_code" :value="__('Verification Code')" />
            <x-text-input id="verification_code" name="verification_code" type="text" required />
            <x-input-error :messages="$errors->get('verification_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>{{ __('Verify Phone') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout> 
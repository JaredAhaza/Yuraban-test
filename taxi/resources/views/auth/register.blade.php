<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="{ 
        role: '{{ old('role', 'customer') }}',
        counties: {{ Illuminate\Support\Js::from($counties) }},
        selectedCounty: '{{ old('county_id', '') }}',
        subCounties: [],
        
        init() {
            this.updateSubCounties();
            this.$watch('selectedCounty', () => this.updateSubCounties());
        },
        
        updateSubCounties() {
            if (this.selectedCounty) {
                const county = this.counties.find(c => c.id == this.selectedCounty);
                this.subCounties = county ? county.sub_counties : [];
            } else {
                this.subCounties = [];
            }
        }
    }">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="Enter your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required placeholder="+254712345678" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Register as')" />
            <select id="role" name="role" class="block mt-1 w-full" required x-model="role">
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="driver" {{ old('role') == 'driver' ? 'selected' : '' }}>Driver</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- County Selection (only for drivers) -->
        <div class="mt-4" x-show="role === 'driver'">
            <x-input-label for="county_id" :value="__('County')" />
            <select id="county_id" name="county_id" class="block mt-1 w-full" x-model="selectedCounty">
                <option value="">Select County</option>
                <template x-for="county in counties" :key="county.id">
                    <option :value="county.id" x-text="county.county_name"></option>
                </template>
            </select>
            <x-input-error :messages="$errors->get('county_id')" class="mt-2" />
        </div>

        <!-- Sub-County Selection (only for drivers) -->
        <div class="mt-4" x-show="role === 'driver' && selectedCounty">
            <x-input-label for="subcounty" :value="__('Sub-County')" />
            <select id="subcounty" name="subcounty" class="block mt-1 w-full" :required="role === 'driver'">
                <option value="">Select Sub-County</option>
                <template x-for="subCounty in subCounties" :key="subCounty">
                    <option :value="subCounty" x-text="subCounty"></option>
                </template>
            </select>
            <x-input-error :messages="$errors->get('subcounty')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
@props(['pharmacy', 'withPassword' => false])

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="name" value="Pharmacy name" />
        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $pharmacy->name)" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="owner_name" value="Owner / contact person" />
        <x-text-input id="owner_name" name="owner_name" type="text" class="block mt-1 w-full" :value="old('owner_name', $pharmacy->owner_name)" required />
        <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone" value="Phone" />
        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $pharmacy->phone)" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="email" value="Login email" />
        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $pharmacy->email)" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
        <p class="mt-1 text-xs text-slate-500">The pharmacy signs in with this email address.</p>
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="address" value="Address" />
        <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address', $pharmacy->address)" />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="city" value="City" />
        <x-text-input id="city" name="city" type="text" class="block mt-1 w-full" :value="old('city', $pharmacy->city)" />
        <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="district" value="District" />
        <x-text-input id="district" name="district" type="text" class="block mt-1 w-full" :value="old('district', $pharmacy->district)" />
        <x-input-error :messages="$errors->get('district')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="province" value="Province" />
        <x-text-input id="province" name="province" type="text" class="block mt-1 w-full" :value="old('province', $pharmacy->province)" />
        <x-input-error :messages="$errors->get('province')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="country" value="Country" />
        <x-text-input id="country" name="country" type="text" class="block mt-1 w-full" :value="old('country', $pharmacy->country ?: 'Nepal')" />
        <x-input-error :messages="$errors->get('country')" class="mt-2" />
    </div>
</div>

@if ($withPassword)
    <div class="mt-6 border-t border-slate-200 pt-6">
        <h3 class="text-sm font-semibold text-slate-900">Pharmacy login password</h3>
        <p class="mt-1 text-xs text-slate-500">The pharmacy user signs in with this password. You can reset it later.</p>

        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirm password" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>
    </div>
@endif

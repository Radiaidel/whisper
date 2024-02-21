<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    @include('profile.partials.top-user-info')
            </div>


            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Section de modification du mot de passe -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    @include('profile.partials.update-password-form')
            </div>

            <!-- Section de suppression de l'utilisateur -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    @include('profile.partials.delete-user-form')
            </div>


        </div>
    </div>
</x-app-layout>
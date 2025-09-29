<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <i class="mr-2 fas fa-user"></i> Mon profil
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nom</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Inscrit le</dt>
                            <dd class="mt-1 text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

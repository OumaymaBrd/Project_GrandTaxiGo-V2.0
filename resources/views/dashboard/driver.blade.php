@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Driver Dashboard') }}</h1>
                <p class="mb-4">{{ __('Welcome to your driver dashboard!') }}</p>

                <div class="bg-indigo-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-indigo-700 mb-2">{{ __('Your Driver Profile') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">{{ __('Name') }}: <span class="font-medium text-gray-900">{{ auth()->user()->name }}</span></p>
                            <p class="text-gray-600">{{ __('Email') }}: <span class="font-medium text-gray-900">{{ auth()->user()->email }}</span></p>
                            <p class="text-gray-600">{{ __('Role') }}: <span class="font-medium text-gray-900">{{ ucfirst(auth()->user()->role) }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Available Rides') }}</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-500 italic">{{ __('No ride requests available at the moment.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


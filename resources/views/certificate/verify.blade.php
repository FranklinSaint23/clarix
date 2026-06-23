@extends('layouts.app')

@section('title', 'Vérification du certificat')

@section('content')
<div class="max-w-lg mx-auto text-center py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-green-200 p-10">
        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-certificate text-4xl text-green-600"></i>
        </div>
        <h1 class="text-2xl font-display font-bold text-green-700 mb-2">Certificat Valide</h1>
        <p class="text-on-surface-variant mb-6">Ce certificat CLARIX est authentique.</p>
        <div class="text-left space-y-3 text-sm">
            <div class="flex justify-between border-b pb-2">
                <span class="text-on-surface-variant">Apprenant</span>
                <span class="font-semibold">{{ $enrollment->user->name }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-on-surface-variant">Formation</span>
                <span class="font-semibold">{{ $enrollment->course->title }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-on-surface-variant">Obtenu le</span>
                <span class="font-semibold">{{ ($enrollment->completed_at ?? $enrollment->updated_at)->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

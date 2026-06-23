@extends('layouts.app')

@section('title', 'Ajouter une ressource')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Ajouter une ressource à la leçon : {{ $lesson->title }}</h1>

    <form action="{{ route('instructor.lessons.resources.store', $lesson) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre de la ressource</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Fichier</label>
            <input type="file" name="file" class="w-full border rounded px-3 py-2" required>
            <p class="text-sm text-gray-500 mt-1">Taille max : 50 Mo</p>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.edit', $lesson->chapter->course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
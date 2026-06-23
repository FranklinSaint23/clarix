@extends('layouts.app')

@section('title', 'Modifier une ressource')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Modifier : {{ $resource->title }}</h1>

    <form action="{{ route('instructor.lessons.resources.update', [$lesson, $resource]) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre</label>
            <input type="text" name="title" value="{{ old('title', $resource->title) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Nouveau fichier (optionnel)</label>
            <input type="file" name="file" class="w-full border rounded px-3 py-2">
            @if($resource->file_path)
                <div class="mt-1 text-sm text-gray-500">Fichier actuel : {{ basename($resource->file_path) }} ({{ $resource->formatted_size }})</div>
            @endif
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.edit', $lesson->chapter->course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
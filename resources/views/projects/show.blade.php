@extends('layouts.app')
@section('content')
    <header class="flex items-center mb-4 py-4">    
        <div class="flex justify-between w-full items-center">
            <p class="text-gray text-sm font-normal">
                <a href="/projects" class="text-gray text-sm font-normal no-underline">My projects</a> / {{ $project->title }}
            </p>     
            <a href="/projects/create" class="button">New Project</a>
        </div>
    </header>

    <main>
        <div class="lg:flex -mx-3">
            <div class="lg:w-3/4 px-3 mb-6">
                
                <div class="mb-8">
                    <h2 class="text-gray font-normal text-lg mb-3">Tasks</h2>  
                    <div class="card mb-3">Lorem ipsum dolor sit amet consectetur adipisicing elit.</div>
                    <div class="card">Voluptatum ullam culpa fuga dolorem blanditiis minus, mollitia vel pariatur! Explicabo, ea.</div>
                </div>
                
            
                <div>
                    <h2 class="text-gray font-normal text-lg mb-3">General notes</h2>  
                    <textarea class="card w-full" style="min-height: 200px;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum ullam culpa fuga dolorem blanditiis minus, mollitia vel pariatur! Explicabo, ea. Blanditiis impedit est quia laudantium nulla eveniet voluptatibus. Tenetur, facilis.</textarea>
                </div>
                
                
            </div>
            

            <div class="lg:w-1/4 px-3">
                @include('projects.card')
            </div>
        </div>
    </main>

    
@endsection
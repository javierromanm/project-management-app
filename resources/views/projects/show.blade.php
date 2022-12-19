@extends('layouts.app')
@section('content')
    <header class="flex items-center mb-4 py-4">    
        <div class="flex justify-between w-full items-center">
            <p class="text-gray text-sm font-normal">
                <a href="/projects" class="text-gray text-sm font-normal no-underline">My projects</a> / {{ $project->title }}
            </p>     
            <a href="{{ $project->path() . '/edit' }}" class="button">Edit Project</a>
        </div>
    </header>

    <main>
        <div class="lg:flex -mx-3">
            <div class="lg:w-3/4 px-3 mb-6">
                
                <div class="mb-8">
                    <h2 class="text-gray font-normal text-lg mb-3">Tasks</h2>  
                    @foreach($project->tasks as $task)
                        <div class="card mb-3">
                            <form method="POST" action="{{ $task->path() }}">
                                @method('PATCH')
                                @csrf
                                <div class="flex">
                                    <input type="text" name="body" value="{{ $task->body }}" class="w-full {{ $task->completed ? 'text-gray' : '' }}">
                                    <input type="checkbox" name="completed" onChange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>        
                                </div>                                                    
                            </form>
                            
                        </div>
                    @endforeach
                    <div class="card mb-3">
                        <form action="{{ $project->path() . '/tasks' }}" method="POST">
                            @csrf
                            <input type="text" name="body" placeholder="Add a new task..." class="w-full">
                        </form>
                    </div>
                </div>
                
            
                <div>
                    <form action="{{ $project->path() }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <h2 class="text-gray font-normal text-lg mb-3">General notes</h2>  
                        <textarea name="notes" class="card w-full" style="min-height: 200px;" placeholder="Make a note">{{ $project->notes }}</textarea>
                        <button type="submit" class="button mt-3">Send</button>
                    </form>
                    @if($errors->any())
                        <div class="field mt-6">
                            @foreach($errors->all() as $error)
                                <li class="text-sm text-red">
                                    {{ $error }}
                                </li>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                
            </div>
            

            <div class="lg:w-1/4 px-3">
                @include('projects.card')
            </div>
        </div>
    </main>

    
@endsection
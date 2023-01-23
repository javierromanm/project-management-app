<div class="card flex flex-col" style="height: 200px">
    <h3 class="font-normal text-xl py-4">
        <a href="{{ $project->path() }}">
            {{ $project->title }}
        </a>                    
    </h3>
    <div class="text-gray mb-4 flex-1">{{ Illuminate\Support\Str::limit($project->description, 100) }}</div>
    <footer>
        <form method="POST" action="{{ $project->path() }}" class="text-right">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs">Delete</button>
        </form>
    </footer>
</div>
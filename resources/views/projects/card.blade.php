<div class="card" style="height: 200px">
    <h3 class="font-normal text-xl py-4">
        <a href="{{ $project->path() }}">
            {{ $project->title }}
        </a>                    
    </h3>
    <div class="text-gray">{{ Illuminate\Support\Str::limit($project->description, 100) }}</div>
</div>
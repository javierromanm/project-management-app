<form method="POST" action="{{ $url }}">
    @csrf
    @method($method)    
    <div class="field">
        <label class="label" for="title">Title</label>
        <div class="control">
            <input 
                type="text" 
                class="input" 
                name="title" 
                placeholder="Title"
                value="{{ $project->title }}">
        </div>
    </div>
    <div class="field">
        <label class="label" for="description">Description</label>
        <div class="control">
            <textarea 
                class="textarea" 
                name="description">{{ $project->description }}</textarea>
        </div>
    </div>
    <div class="field">            
        <div class="control">
            <button type="submit" class="button is-link">{{ $buttonText }}</button>
            <a href="{{ $project->path() }}">Cancel</a>
        </div>
    </div>
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
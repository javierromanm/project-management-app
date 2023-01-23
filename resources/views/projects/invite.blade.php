<div class="card flex flex-col mt-3">
        <h3 class="font-normal text-xl py-4">
            Invite a User         
        </h3>                    
        <form method="POST" action="{{ $project->path() . '/invitations' }}">
            @csrf
            <input type="emal" name="email" class="border border-gray w-full py-2 px-3" placeholder="Email address">
            <div class="mt-3">
                <button type="submit" class="button">Invite</button>
            </div>                        
        </form>
        @include('errors', ['bag' => 'invitations'])
    </div>
</div>
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'completed'];

    protected $touches = ['project'];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }

    public function complete()
    {
        $this->update([
            'completed' => true
        ]);
        $this->project->recordActivity('completed_task');
    }

    public function incomplete()
    {
        $this->update([
            'completed' => false
        ]);
        $this->project->recordActivity('incompleted_task');
    }
}

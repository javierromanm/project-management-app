<?php

namespace App\Models;

use Illuminate\Support\Arr;

trait recordsActivity
{
    public $oldAttributes = [];

    public static function bootRecordsActivity()
    {
        static::updating(function($model) {
            $model->oldAttributes = $model->getOriginal();
        });

        if (isset(static::$recordableEvents)) {
            $recordableEvents = static::$recordableEvents;
        } else {
            $recordableEvents = ['created', 'updated', 'deleted'];
        }        

        foreach($recordableEvents as $event) {
            static::$event(function($model) use ($event) {
                if (class_basename($model) !== 'Project') {
                    $event = $event . '_' . strtolower(class_basename($model));
                }  
                $model->recordActivity($event);          
            });
        }
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    } 
    
    public function recordActivity($description) {
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges(),
            'project_id' => class_basename($this) == 'Project' ? $this->id : $this->project->id
        ]);
    }

    protected function activityChanges () {
        if ($this->wasChanged()) {
            return [
                'before' => Arr::except(array_diff($this->oldAttributes, $this->getAttributes()), 'updated_at'),
                'after' => Arr::except($this->getChanges(), 'updated_at')
            ];
        }
    }

}
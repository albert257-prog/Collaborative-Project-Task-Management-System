<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectAddedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $ownerName;

    public function __construct(Project $project, $ownerName)
    {
        $this->project = $project;
        $this->ownerName = $ownerName;
    }

    public function build()
    {
        return $this->subject('You have been added to a new project: ' . $this->project->name)
                    ->view('emails.project_added');
    }
}
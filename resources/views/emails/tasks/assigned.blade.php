<x-mail::message>
# You have a new task!

Hi {{ $task->assignedUser->name }},

A new task has been assigned to you in the project **"{{ $task->project->name }}"**.

**Task:** {{ $task->title }} <br/>
**Due Date:** {{ $task->due_date ?? 'N/A' }}

You can view the task and other project details by clicking the button below.

<x-mail::button :url="route('project.show', $task->project)">
View Project
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

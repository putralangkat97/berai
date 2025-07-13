<?php

namespace App\Http\Controllers\Berai;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Mail\TaskAssigned;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the user's tasks.
     */
    public function index(Request $request): Response
    {
        $tasks = Task::where('assigned_to_id', Auth::id())
            ->with(['project:id,name', 'assignedUser:id,name,avatar'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('title', 'ILIKE', "%{$search}%");
            })
            ->when($request->input('priority'), function ($query, $priority) {
                if ($priority === 'all') {
                    $query->whereIn('priority', [1, 2, 3, 4]);
                } else {
                    $query->where('priority', $priority);
                }
            })
            ->when($request->input('status'), function ($query, $status) {
                if ($status === 'all') {
                    $query->whereIn('status', [1, 2, 3]);
                } else {
                    $query->where('status', $status);
                }
            })
            ->latest('due_date')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => $task->due_date,
                    'project' => $task->project,
                    'status' => $task->status->message(),
                    'assignedUser' => $task->assignedUser,
                    'priority' => $task->priority->message(),
                ];
            });

        return Inertia::render('app/task/index', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'status']),
            'taskStatuses' => TaskStatus::toArray(),
            'taskPriorities' => TaskPriority::toArray(),
        ]);
    }

    /**
     * Handle an incoming task request
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'assigned_to_id' => [
                    'required',
                    'integer',
                    Rule::exists('project_user', 'user_id')->where('project_id', $project->id),
                ],
                'priority' => ['required', Rule::enum(TaskPriority::class)],
            ]);

            if ($validated) {
                $task = $project->tasks()->create($validated);
                DB::commit();

                // Send email notification and,
                // Check if the user was actually assigned
                $task->load('assignedUser', 'project');
                if ($task->assignedUser) {
                    Mail::to($task->assignedUser)->send(new TaskAssigned($task));
                }

                return redirect()
                    ->route('project.show', $project)
                    ->with('success', 'Task created successfully');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('task-create', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create task. Please try again.');
        }
    }

    /**
     * Update the specific task status
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        // Check if the allowed user can update the task
        Gate::authorize('update', $task);

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'status' => ['required', Rule::enum(TaskStatus::class)],
            ]);

            if ($validated) {
                $task->update($validated);
                DB::commit();

                return redirect()
                    ->route('project.show', $task->project_id)
                    ->with('success', 'Task status updated');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('task-status-update', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update status. Please try again.');
        }
    }

    /**
     * Update the specific task priority
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function updatePriority(Request $request, Task $task): RedirectResponse
    {
        // Check if the allowed user can update the task
        Gate::authorize('update', $task);

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'priority' => ['required', Rule::enum(TaskPriority::class)],
            ]);

            if ($validated) {
                $task->update($validated);
                DB::commit();

                return redirect()
                    ->route('project.show', $task->project_id)
                    ->with('success', 'Task priority updated');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('task-priority-update', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update the priority. Please try again.');
        }
    }
}

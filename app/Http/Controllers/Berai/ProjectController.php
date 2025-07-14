<?php

namespace App\Http\Controllers\Berai;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ProjectController extends Controller
{
    /**
     * Display Project list
     */
    public function index(Request $request): Response
    {
        // projects
        $projects = Auth::user()
            ->projects()
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', TaskStatus::COMPLETED);
                },
            ])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'ILIKE', "%{$search}%");
            })
            ->latest()
            ->get();

        return Inertia::render('app/project/index', [
            'projects' => $projects,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Display project form
     */
    public function create(): Response
    {
        return Inertia::render('app/project/form');
    }

    /**
     * Handle an incoming project request
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function store(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validated) {
                $current_user = Auth::user();
                $project = $current_user->ownedProjects()->create($validated);

                $project->members()->attach($current_user);
                DB::commit();

                return redirect()
                    ->route('project.show', [
                        'project' => $project,
                    ])
                    ->with('success', 'Project created');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('project-create', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create project. Please try again.');
        }
    }

    /**
     * Display the specific project
     */
    public function show(Request $request, Project $project): Response
    {
        $project->load([
            'owner',
            'members',
            'tasks' => function ($query) use ($request) {
                $query
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
                    ->with('assignedUser:id,name,avatar')
                    ->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'asc');
            },
        ]);

        $activities = Activity::where('project_id', $project->id)
            ->with(['causer', 'subject'])
            ->latest()
            ->limit(25)
            ->get();

        return Inertia::render('app/project/show', [
            'project' => $project,
            'activities' => $activities,
            'taskStatuses' => TaskStatus::toArray(),
            'taskPriorities' => TaskPriority::toArray(),
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    /**
     * Handle an incoming project update request
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validated) {
                $project->update($validated);
                DB::commit();

                return back()->with('success', 'Project details have been updated.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('project-update', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update project. Please try again.');
        }
    }

    /**
     * Delete project
     */
    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()->route('project.index')->with('success', 'Project has been deleted');
    }
}

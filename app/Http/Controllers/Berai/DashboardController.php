<?php

namespace App\Http\Controllers\Berai;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display dashboard content
     */
    public function index(): Response
    {
        $current_user = Auth::user();

        // projects
        $projects = $current_user
            ->projects()
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', TaskStatus::COMPLETED);
                },
            ])
            ->latest()
            ->get();

        // open tasks
        $open_tasks = Task::where('assigned_to_id', $current_user->id)
            ->where('status', '!=', TaskStatus::COMPLETED)
            ->with('project:id,name')
            ->latest('due_date')
            ->get();

        return Inertia::render('app/dashboard', [
            'projects' => $projects,
            'openTasks' => $open_tasks,
        ]);
    }

    /**
     * Display welcome page
     */
    public function welcome(): Response|RedirectResponse
    {
        if (! session('new_user')) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('app/welcome');
    }
}

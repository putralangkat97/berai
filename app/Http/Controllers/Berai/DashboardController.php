<?php

namespace App\Http\Controllers\Berai;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->limit(5)
            ->get();

        // overall task status
        $task_status_count = Task::whereIn('project_id', $current_user->projects()->pluck('id'))
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->mapWithKeys(function ($count, $status_value) {
                return [
                    TaskStatus::from($status_value)->message() => $count,
                ];
            });

        // get upcoming due date task (7 days)
        $upcoming_tasks = Task::where('assigned_to_id', $current_user->id)
            ->where('status', '!=', TaskStatus::COMPLETED)
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->orderBy('due_date', 'asc')
            ->with('project:id,name')
            ->get();

        // get user's task completion rate
        $total_user_tasks = Task::where('assigned_to_id', $current_user->id)->count();
        $completed_user_tasks = Task::where('assigned_to_id', $current_user->id)
            ->where('status', TaskStatus::COMPLETED)
            ->count();
        $completion_rate =
            $total_user_tasks > 0 ? round(($completed_user_tasks / $total_user_tasks) * 100) : 0;

        return Inertia::render('app/dashboard', [
            'projects' => $projects,
            'openTasks' => $open_tasks,
            'upcomingTasks' => $upcoming_tasks,
            'analytics' => [
                'completionRate' => $completion_rate,
                'totalUserTasks' => $total_user_tasks,
                'taskStatusCount' => $task_status_count,
                'totalProjects' => $current_user->projects()->count(),
            ],
        ]);
    }

    /**
     * Display welcome page
     */
    public function welcome(): Response|RedirectResponse
    {
        if (!session('new_user')) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('app/welcome');
    }
}

<?php

namespace App\Http\Controllers\Berai;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = Auth::user()->projects()
            ->latest()
            ->get();

        return Inertia::render('app/project/index', [
            'projects' => $projects
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
            $project = $current_user->ownedProjects()
                ->create($validated);

            $project->members()->attach($current_user);
            DB::commit();

            return redirect()->route('project.show', [
                'project' => $project,
            ])->with('success', 'Project created');
        }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('project_create', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Display the specific project
     *
     * @param Project $project
     */
    public function show(Project $project): Response
    {
        $project->load('owner', 'members');

        return Inertia::render('app/project/show', [
            'project' => $project,
        ]);
    }
}

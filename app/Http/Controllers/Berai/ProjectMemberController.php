<?php

namespace App\Http\Controllers\Berai;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectMemberController extends Controller
{
    /**
     * Handle invite member form
     */
    public function __invoke(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('add-member', $project);

        $validated = $request->validate([
            'email' => ['required', 'email', Rule::exists('users', 'email')],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($project->members->contains($user)) {
            return back()->withErrors([
                'email' => 'This user is already a member of the project',
            ]);
        }

        $project->members()->attach($user);

        return redirect()
            ->route('project.show', $project)
            ->with('success', 'Member added successfully');
    }
}

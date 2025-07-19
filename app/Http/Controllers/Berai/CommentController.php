<?php

namespace App\Http\Controllers\Berai;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('addComment', $task);

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'body' => 'required|string',
            ]);

            if ($validated) {
                $task->comments()->create([
                    'user_id' => Auth::user()->id,
                    'body' => $validated['body'],
                ]);
                DB::commit();

                return back()->with('success', 'Comment posted.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('post-comment', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to post comment. Please try again.');
        }
    }
}

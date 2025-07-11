<?php

namespace App\Http\Controllers\Berai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        // projects
        $projects = Auth::user()->projects()
            ->latest()
            ->get();

        return Inertia::render('app/dashboard', [
            'projects' => $projects
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\JobRole;
use App\Models\Resume;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'totalCandidates' => User::candidates()->count(),
            'totalAdmins' => User::admins()->count(),
            'activeJobRoles' => JobRole::active()->count(),
            'uploadedResumes' => Resume::count(),
            'completedAnalyses' => Resume::completed()->count(),
        ]);
    }
}

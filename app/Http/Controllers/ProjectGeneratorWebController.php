<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectGeneratorWebController extends Controller
{
    public function create()
    {
        return view('page.project.create');
    }

    public function viewer($id)
    {
        return view('page.project.viewer', compact('id'));
    }

    public function history(Request $request)
    {
        $projects = $request->user()->projects()->latest()->get();
        return view('page.project.history', compact('projects'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::where('user_id', Auth::id())->get();
        return $this->showAll($projects);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'tasks' => 'nullable|array',
                'tasks.*.title' => 'required|string|max:255',
                'tasks.*.description' => 'nullable|string',
                'tasks.*.status' => 'required|string',
                'tasks.*.user_id' => 'required',
            ]);

            $project = Project::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
                'user_id' => Auth::id(),
            ]);

            // Check for tasks
            if (!empty($validatedData['tasks'])) {
                foreach ($validatedData['tasks'] as $taskData) {
                    $project->tasks()->create([
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'status' => $taskData['status'],
                        'user_id' => $taskData['user_id'],
                    ]);
                }
            }

            return $this->showOne($project->load('tasks'), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $project = Project::where('user_id', Auth::id())->with('tasks')->find($id);

        if (!$project) {
            return $this->errorResponse(['message' => 'Project not found'], 404);
        }

        return $this->showOne($project);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
            ]);

            $project = Project::where('user_id', Auth::id())->find($id);

            if (!$project) {
                return $this->errorResponse(['message' => 'Project not found'], 404);
            }

            $updateData = array_filter($validatedData, fn($value) => !is_null($value));

            if (!empty($updateData)) {
                $project->update($updateData);
            }

            return $this->showOne($project);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(['errors' => $e->errors()], 422);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $project = Project::where('user_id', Auth::id())->with('tasks')->find($id);

        if (!$project) {
            return $this->errorResponse(['message' => 'Project not found'], 404);
        }

        $allPending = $project->tasks->every(function ($task) {
            return $task->status === 'pending';
        });

        if (!$allPending) {
            return $this->errorResponse(['message' => 'Only projects without tasks or with pending tasks can be deleted.'], 400);
        }

        $project->delete();

        return $this->showMessage(['message' => 'Project deleted']);
    }
    
}

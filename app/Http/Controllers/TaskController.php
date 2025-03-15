<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return $this->showAll($tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'status' => 'required|string|in:pending,in progress,completed',
                'project_id' => 'required|exists:projects,id', 
                'user_id' => 'required|exists:users,id',  
            ]);

            $task = Task::create($validatedData);

            return $this->showOne($task, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }


    /**
     * Display the specified resource.
     */
    // public function show(Task $task)
    public function show($id)
    {
        $task = Task::find($id);

        // If the task is not found, return an error
        if (!$task) {
            return $this->errorResponse(['message' => 'Task not found'], 404);
        }

        return $this->showOne($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
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
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'due_date' => 'sometimes|nullable|date',
                'status' => 'sometimes|required|string|in:pending,in progress,completed',
                'project_id' => 'sometimes|required|exists:projects,id',
                'user_id' => 'sometimes|required|exists:users,id',
            ]);

            $task = Task::find($id);

            if (!$task) {
                return $this->errorResponse(['message' => 'Task not found'], 404);
            }

            $updateData = array_filter($validatedData, fn($value) => !is_null($value));

            if (!empty($updateData)) {
                $task->update($updateData);
            }

            return $this->showOne($task);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        $project = Project::find($task->project_id);

        if ($project->user_id != Auth::id()) {
            return $this->errorResponse(['message' => 'Unauthorized'], 401);
        }

        // If the task is not found, return an error
        if (!$task) {
            return $this->errorResponse(['message' => 'Task not found'], 404);
        }

        $task->delete();

        $this->showMessage(['message' => 'Task deleted successfully'], 200);
    }
}

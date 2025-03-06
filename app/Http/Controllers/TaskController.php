<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskController\storeRequest;
use App\Http\Requests\TaskController\updateRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $tasks = Task::all();

        return response()->json([
            'tasks' => $tasks,
            'success' => true,
        ]);

    }
    public function store(storeRequest $request)
    {
        $user = Auth::user();

        $task = Task::create([
            'title'       => $request->get('title'),
            'description' => $request->get('description'),
            'user_id' => auth()->id(),

      ]);
        return response()->json([
            'success' => true,
            'task' => $task,
            'massage' => 'task created successfully',
        ]);
    }
    public function update(updateRequest $request)
    {
        $user = Auth::user();
        $tasks = Task::update([
            'title'       => $request->get('title'),
            'description' => $request->get('description'),
        ]);
        return response()->json([
            'success' => true,
            'task' => $tasks,
            'massage' => 'Task updated successfully',
        ]);
    }
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'task' => $task,
        ]);
    }
    public function done(DoneRequest $request , Task $task)
    {
        Auth::user()->task()->whereId($task->id)->update([

            'done' => $request->post('done'),
        ]);
        return response()->json([
            'success'=> true

        ]);
    }

}

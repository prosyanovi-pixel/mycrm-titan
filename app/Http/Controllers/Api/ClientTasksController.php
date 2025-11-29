<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientTasksController extends Controller
{
    public function index($client)
    {
        return response()->json(['message' => 'Tasks index']);
    }

    public function store(Request $request, $client)
    {
        return response()->json(['message' => 'Task created']);
    }

    public function show($client, $task)
    {
        return response()->json(['message' => 'Task show']);
    }

    public function update(Request $request, $client, $task)
    {
        return response()->json(['message' => 'Task updated']);
    }

    public function destroy($client, $task)
    {
        return response()->json(['message' => 'Task deleted']);
    }
}

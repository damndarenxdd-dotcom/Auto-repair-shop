<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTaskJob;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Task::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json([
            'data' => $query->paginate($request->input('per_page', 15)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:100'],
            'payload' => ['nullable', 'array'],
            'queue' => ['nullable', 'string', 'max:100'],
        ]);

        $task = Task::create([
            'user_id' => $request->user()?->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'status' => 'queued',
            'queue_name' => $validated['queue'] ?? 'tasks',
            'payload' => $validated['payload'] ?? [],
        ]);

        ProcessTaskJob::dispatch($task->id);

        return response()->json([
            'data' => $task->fresh(),
            'message' => 'Task queued successfully.',
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'data' => $task->fresh(),
        ]);
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:queued,processing,completed,failed,cancelled'],
            'result' => ['nullable', 'array'],
            'error_message' => ['nullable', 'string'],
        ]);

        $task->update([
            'status' => $validated['status'],
            'result' => $validated['result'] ?? $task->result,
            'error_message' => $validated['error_message'] ?? $task->error_message,
            'started_at' => $task->started_at ?? ($validated['status'] === 'processing' ? now() : $task->started_at),
            'completed_at' => ($validated['status'] === 'completed' || $validated['status'] === 'failed' || $validated['status'] === 'cancelled')
                ? ($task->completed_at ?? now())
                : $task->completed_at,
        ]);

        return response()->json([
            'data' => $task->fresh(),
            'message' => 'Task status updated successfully.',
        ]);
    }
}

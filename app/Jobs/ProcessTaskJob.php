<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $taskId)
    {
        $this->onQueue('tasks');
    }

    public function handle(): void
    {
        $task = Task::find($this->taskId);

        if (! $task) {
            return;
        }

        $task->update([
            'status' => 'processing',
            'queue_name' => $this->queue ?: 'tasks',
            'started_at' => now(),
        ]);

        try {
            $result = [
                'task_id' => $task->id,
                'type' => $task->type,
                'status' => 'completed',
                'processed_at' => now()->toISOString(),
                'payload' => $task->payload ?? [],
                'message' => 'Task completed successfully.',
            ];

            $task->update([
                'status' => 'completed',
                'result' => $result,
                'completed_at' => now(),
                'error_message' => null,
            ]);
        } catch (\Throwable $throwable) {
            $task->update([
                'status' => 'failed',
                'error_message' => $throwable->getMessage(),
                'completed_at' => now(),
            ]);

            throw $throwable;
        }
    }
}

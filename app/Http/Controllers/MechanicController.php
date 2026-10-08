<?php

namespace App\Http\Controllers;

use App\Models\RepairJob;
use App\Models\Notification;
use Illuminate\Http\Request;

class MechanicController extends Controller
{
    public function dashboard()
    {
        $userId = auth()->id();
        $assignedJobs = RepairJob::where('mechanic_id', $userId)->count();
        $inProgressJobs = RepairJob::where('mechanic_id', $userId)->where('status', 'in-progress')->count();
        $completedJobs = RepairJob::where('mechanic_id', $userId)->where('status', 'completed')->count();
        $recentJobs = RepairJob::where('mechanic_id', $userId)->latest()->limit(5)->get();

        return view('mechanic.dashboard', compact('assignedJobs', 'inProgressJobs', 'completedJobs', 'recentJobs'));
    }

    public function jobs()
    {
        $jobs = RepairJob::with('customer')
            ->where('mechanic_id', auth()->id())
            ->paginate(15);
        return view('mechanic.jobs.index', compact('jobs'));
    }

    public function viewJob($id)
    {
        $job = RepairJob::with('services', 'customer', 'manager')->findOrFail($id);
        $this->authorize('view', $job);
        return view('mechanic.jobs.show', compact('job'));
    }

    public function updateJobStatus(Request $request, $id)
    {
        $job = RepairJob::findOrFail($id);
        $this->authorize('update', $job);

        $validated = $request->validate([
            'status' => 'required|in:assigned,in-progress,completed',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $job->status;
        $job->update($validated);

        if ($validated['status'] === 'in-progress' && $oldStatus !== 'in-progress') {
            $job->update(['start_date' => now()]);

            // Notify customer and manager
            Notification::create([
                'user_id' => $job->customer_id,
                'repair_job_id' => $job->id,
                'title' => 'Repair In Progress',
                'message' => 'Your repair job is now in progress',
                'type' => 'job_status_change',
            ]);

            if ($job->manager_id) {
                Notification::create([
                    'user_id' => $job->manager_id,
                    'repair_job_id' => $job->id,
                    'title' => 'Repair Started',
                    'message' => 'Job for ' . $job->vehicle_model . ' has started',
                    'type' => 'job_status_change',
                ]);
            }
        }

        if ($validated['status'] === 'completed') {
            $job->update(['completion_date' => now()]);

            // Notify customer and manager
            Notification::create([
                'user_id' => $job->customer_id,
                'repair_job_id' => $job->id,
                'title' => 'Repair Completed',
                'message' => 'Your repair has been completed successfully',
                'type' => 'job_completed',
            ]);
        }

        return redirect()->route('mechanic.jobs.index')->with('success', 'Job status updated successfully');
    }

    public function addNotes(Request $request, $id)
    {
        $job = RepairJob::findOrFail($id);
        $this->authorize('update', $job);

        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $job->update(['notes' => $job->notes . "\n" . $validated['notes']]);
        return back()->with('success', 'Notes added successfully');
    }
}

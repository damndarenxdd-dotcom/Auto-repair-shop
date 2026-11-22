<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\RepairJob;
use App\Models\Invoice;
use Illuminate\Http\Request;

class MechanicController extends \App\Http\Controllers\Controller
{
    public function dashboard(Request $request)
    {
        $userId = auth()->id();
        $assignedJobs = RepairJob::where('mechanic_id', $userId)->count();
        $inProgressJobs = RepairJob::where('mechanic_id', $userId)->where('status', 'in-progress')->count();
        $completedJobs = RepairJob::where('mechanic_id', $userId)->where('status', 'completed')->count();
        $recentJobs = RepairJob::where('mechanic_id', $userId)->latest()->limit(5)->get();
        [$weather, $weather_error] = $this->weatherForUser($request);

        return view('mechanic.dashboard.index', compact('assignedJobs', 'inProgressJobs', 'completedJobs', 'recentJobs', 'weather', 'weather_error'));
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
                    'message' => 'Job for '.$job->vehicle_model.' has started',
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

            // Create invoice automatically if one does not exist
            $existing = Invoice::where('repair_job_id', $job->id)->first();
            if (! $existing) {
                $subtotal = $job->actual_cost ?? $job->estimated_cost ?? 0;
                $tax = 0;
                $invoice = new Invoice([
                    'repair_job_id' => $job->id,
                    'customer_id' => $job->customer_id,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $subtotal + $tax,
                    'status' => 'paid',
                    'paid_date' => now(),
                ]);
                $invoice->invoice_number = $invoice->generateInvoiceNumber();
                $invoice->save();
            }
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

        $job->update(['notes' => $job->notes."\n".$validated['notes']]);

        return back()->with('success', 'Notes added successfully');
    }
}

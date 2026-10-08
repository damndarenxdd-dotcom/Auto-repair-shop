<?php

namespace App\Http\Controllers;

use App\Models\RepairJob;
use App\Models\User;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Notification;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function dashboard()
    {
        $userId = auth()->id();
        $assignedJobs = RepairJob::where('manager_id', $userId)->count();
        $inProgressJobs = RepairJob::where('manager_id', $userId)->where('status', 'in-progress')->count();
        $completedJobs = RepairJob::where('manager_id', $userId)->where('status', 'completed')->count();
        $pendingJobs = RepairJob::where('manager_id', $userId)->where('status', 'pending')->count();

        return view('manager.dashboard', compact('assignedJobs', 'inProgressJobs', 'completedJobs', 'pendingJobs'));
    }

    // Repair Jobs Management
    public function jobs()
    {
        $jobs = RepairJob::with('customer', 'mechanic')
            ->where('manager_id', auth()->id())
            ->paginate(15);
        return view('manager.jobs.index', compact('jobs'));
    }

    public function createJob()
    {
        $customers = User::where('role_id', 4)->get(); // customers
        $mechanics = User::where('role_id', 3)->get(); // mechanics
        $services = Service::where('is_active', true)->get();
        return view('manager.jobs.create', compact('customers', 'mechanics', 'services'));
    }

    public function storeJob(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'mechanic_id' => 'nullable|exists:users,id',
            'vehicle_model' => 'required|string',
            'license_plate' => 'required|string',
            'year' => 'nullable|integer',
            'description' => 'required|string',
            'estimated_cost' => 'nullable|numeric',
        ]);

        $validated['manager_id'] = auth()->id();
        $job = RepairJob::create($validated);

        // Notify customer
        Notification::create([
            'user_id' => $validated['customer_id'],
            'repair_job_id' => $job->id,
            'title' => 'Repair Job Created',
            'message' => 'A new repair job has been created for your vehicle: ' . $validated['vehicle_model'],
            'type' => 'job_status_change',
        ]);

        return redirect()->route('manager.jobs.index')->with('success', 'Job created successfully');
    }

    public function editJob($id)
    {
        $job = RepairJob::with('services')->findOrFail($id);
        $this->authorize('manage', $job);
        $mechanics = User::where('role_id', 3)->get();
        $services = Service::where('is_active', true)->get();
        return view('manager.jobs.edit', compact('job', 'mechanics', 'services'));
    }

    public function updateJob(Request $request, $id)
    {
        $job = RepairJob::findOrFail($id);
        $this->authorize('manage', $job);

        $validated = $request->validate([
            'mechanic_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,assigned,in-progress,completed,cancelled',
            'estimated_cost' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $job->status;
        $job->update($validated);

        // Notify mechanic if assigned
        if ($validated['mechanic_id'] && $oldStatus !== 'assigned') {
            Notification::create([
                'user_id' => $validated['mechanic_id'],
                'repair_job_id' => $job->id,
                'title' => 'New Job Assigned',
                'message' => 'A repair job has been assigned to you: ' . $job->vehicle_model,
                'type' => 'job_assigned',
            ]);
        }

        return redirect()->route('manager.jobs.index')->with('success', 'Job updated successfully');
    }

    public function deleteJob($id)
    {
        $job = RepairJob::findOrFail($id);
        $this->authorize('manage', $job);
        $job->delete();
        return redirect()->route('manager.jobs.index')->with('success', 'Job deleted successfully');
    }

    // Invoices Management
    public function invoices()
    {
        $invoices = Invoice::with('customer', 'repairJob')->paginate(15);
        return view('manager.invoices.index', compact('invoices'));
    }

    public function createInvoice($jobId)
    {
        $job = RepairJob::with('services')->findOrFail($jobId);
        return view('manager.invoices.create', compact('job'));
    }

    public function storeInvoice(Request $request, $jobId)
    {
        $job = RepairJob::findOrFail($jobId);
        $validated = $request->validate([
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,overdue',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['repair_job_id'] = $jobId;
        $validated['customer_id'] = $job->customer_id;
        $validated['total'] = ($validated['subtotal'] ?? 0) + ($validated['tax'] ?? 0);

        $invoice = new Invoice($validated);
        $invoice->invoice_number = $invoice->generateInvoiceNumber();
        $invoice->save();

        return redirect()->route('manager.invoices.index')->with('success', 'Invoice created successfully');
    }
}

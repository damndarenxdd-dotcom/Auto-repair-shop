<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\RepairJob;
use App\Models\Service;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        $userId = auth()->id();
        $assignedJobs = RepairJob::where('manager_id', $userId)->count();
        $inProgressJobs = RepairJob::where('manager_id', $userId)->where('status', 'in-progress')->count();
        $completedJobs = RepairJob::where('manager_id', $userId)->where('status', 'completed')->count();
        $pendingJobs = RepairJob::where('manager_id', $userId)->where('status', 'pending')->count();

        $monthlyPerformance = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $key = $month->format('M');
            $monthlyLabels[] = $key;
            $monthlyPerformance[] = RepairJob::where('manager_id', $userId)
                ->where('created_at', '>=', $month->copy()->startOfMonth())
                ->where('created_at', '<=', $month->copy()->endOfMonth())
                ->count();
        }

        $statusSummary = [
            'pending' => RepairJob::where('manager_id', $userId)->where('status', 'pending')->count(),
            'assigned' => RepairJob::where('manager_id', $userId)->where('status', 'assigned')->count(),
            'in-progress' => RepairJob::where('manager_id', $userId)->where('status', 'in-progress')->count(),
            'completed' => RepairJob::where('manager_id', $userId)->where('status', 'completed')->count(),
        ];

        [$weather, $weather_error] = $this->weatherForUser($request);

        return view('manager.dashboard.index', compact('assignedJobs', 'inProgressJobs', 'completedJobs', 'pendingJobs', 'weather', 'weather_error', 'monthlyLabels', 'monthlyPerformance', 'statusSummary'));
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
        $customers = User::whereHas('role', fn ($query) => $query->where('name', 'customer'))->get();
        $mechanics = User::whereHas('role', fn ($query) => $query->where('name', 'mechanic'))->get();
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
            'message' => 'A new repair job has been created for your vehicle: '.$validated['vehicle_model'],
            'type' => 'job_status_change',
        ]);

        return redirect()->route('manager.jobs.index')->with('success', 'Job created successfully');
    }

    public function editJob($id)
    {
        $job = RepairJob::with('services')->findOrFail($id);
        $this->authorize('manage', $job);
        $mechanics = User::whereHas('role', fn ($query) => $query->where('name', 'mechanic'))->get();
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
                'message' => 'A repair job has been assigned to you: '.$job->vehicle_model,
                'type' => 'job_assigned',
            ]);
        }

        // If manager marked it completed, set completion date and create invoice
        if (($validated['status'] ?? null) === 'completed' && $oldStatus !== 'completed') {
            $job->update(['completion_date' => now()]);

            // Notify customer
            Notification::create([
                'user_id' => $job->customer_id,
                'repair_job_id' => $job->id,
                'title' => 'Repair Completed',
                'message' => 'Your repair has been completed',
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

    // Task Monitoring
    public function taskDashboard(Request $request)
    {
        $statusFilter = $request->input('status');
        $typeFilter = $request->input('type');

        $query = Task::query();
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        $tasks = $query->where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total' => Task::count(),
            'queued' => Task::where('status', 'queued')->count(),
            'processing' => Task::where('status', 'processing')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'failed' => Task::where('status', 'failed')->count(),
        ];

        $taskTypes = Task::select('type')->distinct()->orderBy('type')->pluck('type');
        $failureSummary = DB::table('failed_jobs')
            ->selectRaw('queue, COUNT(*) as failures')
            ->groupBy('queue')
            ->orderByDesc('failures')
            ->get();

        return view('manager.tasks.dashboard', compact('tasks', 'stats', 'failureSummary', 'taskTypes', 'statusFilter', 'typeFilter'));
    }

    public function taskHistory(Request $request)
    {
        $statusFilter = $request->input('status');
        $typeFilter = $request->input('type');

        $query = Task::query();
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        $tasks = $query->latest()->paginate(15)->appends($request->only(['status', 'type']));

        return view('manager.tasks.history', compact('tasks', 'statusFilter', 'typeFilter'));
    }

    public function taskFailures()
    {
        $failures = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->paginate(15);

        return view('manager.tasks.failures', compact('failures'));
    }

    public function retryFailedJob($id)
    {
        $failure = DB::table('failed_jobs')->where('id', $id)->first();

        if (! $failure) {
            return redirect()->route('manager.tasks.failures')->with('error', 'Failed job not found.');
        }

        Artisan::call('queue:retry', ['id' => [$failure->uuid]]);

        return redirect()->route('manager.tasks.failures')->with('success', 'Failed job has been queued for retry.');
    }
}

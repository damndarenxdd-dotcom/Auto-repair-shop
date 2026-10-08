<?php

namespace App\Http\Controllers;

use App\Models\RepairJob;
use App\Models\Notification;
use App\Models\Invoice;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $userId = auth()->id();
        $totalJobs = RepairJob::where('customer_id', $userId)->count();
        $completedJobs = RepairJob::where('customer_id', $userId)->where('status', 'completed')->count();
        $inProgressJobs = RepairJob::where('customer_id', $userId)->where('status', 'in-progress')->count();
        $pendingJobs = RepairJob::where('customer_id', $userId)->where('status', 'pending')->count();
        $unreadNotifications = Notification::where('user_id', $userId)->where('read', false)->count();

        return view('customer.dashboard', compact(
            'totalJobs',
            'completedJobs',
            'inProgressJobs',
            'pendingJobs',
            'unreadNotifications'
        ));
    }

    public function jobs()
    {
        $jobs = RepairJob::where('customer_id', auth()->id())
            ->latest()
            ->paginate(15);
        return view('customer.jobs.index', compact('jobs'));
    }

    public function viewJob($id)
    {
        $job = RepairJob::with('services', 'mechanic', 'manager')->findOrFail($id);
        $this->authorize('view', $job);
        return view('customer.jobs.show', compact('job'));
    }

    public function requestJob()
    {
        return view('customer.jobs.request');
    }

    public function submitJobRequest(Request $request)
    {
        $validated = $request->validate([
            'vehicle_model' => 'required|string|max:255',
            'license_plate' => 'required|string|max:20',
            'year' => 'nullable|integer|min:1900',
            'description' => 'required|string|min:10',
        ]);

        $validated['customer_id'] = auth()->id();
        $validated['status'] = 'pending';

        $job = RepairJob::create($validated);

        // Notify managers
        $managers = \App\Models\User::where('role_id', 2)->get();
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'repair_job_id' => $job->id,
                'title' => 'New Job Request',
                'message' => 'New repair request for ' . $validated['vehicle_model'],
                'type' => 'job_status_change',
            ]);
        }

        return redirect()->route('customer.jobs')->with('success', 'Job request submitted. A manager will review it soon.');
    }

    public function notifications()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
        return view('customer.notifications.index', compact('notifications'));
    }

    public function markNotificationAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('view', $notification);
        $notification->markAsRead();
        return back()->with('success', 'Notification marked as read');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);
        return back()->with('success', 'All notifications marked as read');
    }

    public function invoices()
    {
        $invoices = Invoice::where('customer_id', auth()->id())
            ->latest()
            ->paginate(15);
        return view('customer.invoices.index', compact('invoices'));
    }

    public function viewInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('view', $invoice);
        return view('customer.invoices.show', compact('invoice'));
    }

    public function downloadInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('view', $invoice);
        // Generate PDF or return file
        return view('customer.invoices.pdf', compact('invoice'));
    }
}

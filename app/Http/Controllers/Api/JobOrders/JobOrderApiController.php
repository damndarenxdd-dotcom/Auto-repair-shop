<?php

namespace App\Http\Controllers\Api\JobOrders;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobOrderResource;
use App\Models\RepairJob;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class JobOrderApiController extends Controller
{
    /**
     * Display a listing of repair jobs.
     */
    public function index(Request $request): ResourceCollection
    {
        $query = RepairJob::query()->with(['customer', 'mechanic', 'manager', 'services']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('mechanic_id')) {
            $query->where('mechanic_id', $request->mechanic_id);
        }

        if ($request->filled('manager_id')) {
            $query->where('manager_id', $request->manager_id);
        }

        if ($request->filled('vehicle_model')) {
            $query->where('vehicle_model', 'like', '%'.$request->vehicle_model.'%');
        }

        $jobs = $query->latest()->paginate($request->input('per_page', 15));

        return JobOrderResource::collection($jobs);
    }

    /**
     * Store a newly created repair job.
     */
    public function store(Request $request): mixed
    {
        $validated = $this->validateJobOrderPayload($request);

        $customer = User::findOrFail($validated['customer_id']);
        if (! $customer->hasRole('customer')) {
            return response()->json([
                'message' => 'Selected customer is not a valid customer account.',
            ], 422);
        }

        if (! empty($validated['mechanic_id'])) {
            $mechanic = User::findOrFail($validated['mechanic_id']);
            if (! $mechanic->hasRole('mechanic')) {
                return response()->json([
                    'message' => 'Selected mechanic is not a valid mechanic account.',
                ], 422);
            }
        }

        if (! empty($validated['manager_id'])) {
            $manager = User::findOrFail($validated['manager_id']);
            if (! $manager->hasRole('manager')) {
                return response()->json([
                    'message' => 'Selected manager is not a valid manager account.',
                ], 422);
            }
        }

        $job = RepairJob::create($validated);

        if (isset($validated['services'])) {
            $this->syncServicesForJob($job, $validated['services']);
        }

        return (new JobOrderResource($job->fresh(['customer', 'mechanic', 'manager', 'services'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified repair job.
     */
    public function show(RepairJob $repairJob): JsonResource
    {
        $repairJob->load(['customer', 'mechanic', 'manager', 'services']);

        return new JobOrderResource($repairJob);
    }

    /**
     * Update the specified repair job.
     */
    public function update(Request $request, RepairJob $repairJob): mixed
    {
        $validated = $this->validateJobOrderPayload($request, true);

        if (isset($validated['customer_id'])) {
            $customer = User::findOrFail($validated['customer_id']);
            if (! $customer->hasRole('customer')) {
                return response()->json([
                    'message' => 'Selected customer is not a valid customer account.',
                ], 422);
            }
        }

        if (array_key_exists('mechanic_id', $validated) && ! empty($validated['mechanic_id'])) {
            $mechanic = User::findOrFail($validated['mechanic_id']);
            if (! $mechanic->hasRole('mechanic')) {
                return response()->json([
                    'message' => 'Selected mechanic is not a valid mechanic account.',
                ], 422);
            }
        }

        if (array_key_exists('manager_id', $validated) && ! empty($validated['manager_id'])) {
            $manager = User::findOrFail($validated['manager_id']);
            if (! $manager->hasRole('manager')) {
                return response()->json([
                    'message' => 'Selected manager is not a valid manager account.',
                ], 422);
            }
        }

        $repairJob->update($validated);

        if (array_key_exists('services', $validated)) {
            $this->syncServicesForJob($repairJob, $validated['services']);
        }

        return new JobOrderResource($repairJob->fresh(['customer', 'mechanic', 'manager', 'services']));
    }

    /**
     * Sync the services for an existing job order.
     */
    public function syncServices(Request $request, RepairJob $repairJob): JsonResource
    {
        $validated = $request->validate([
            'services' => ['required', 'array'],
            'services.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'services.*.quantity' => ['nullable', 'integer', 'min:1'],
            'services.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->syncServicesForJob($repairJob, $validated['services']);

        return new JobOrderResource($repairJob->fresh(['customer', 'mechanic', 'manager', 'services']));
    }

    /**
     * Remove the specified repair job.
     */
    public function destroy(RepairJob $repairJob): JsonResponse
    {
        $repairJob->delete();

        return response()->json(null, 204);
    }

    protected function validateJobOrderPayload(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'customer_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:users,id'],
            'mechanic_id' => ['nullable', 'exists:users,id'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'vehicle_model' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'license_plate' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'description' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'status' => ['nullable', 'in:pending,assigned,in-progress,completed,cancelled'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];

        if ($isUpdate || $request->has('services')) {
            $rules['services'] = ['sometimes', 'array'];
            $rules['services.*.service_id'] = ['required', 'integer', 'exists:services,id'];
            $rules['services.*.quantity'] = ['nullable', 'integer', 'min:1'];
            $rules['services.*.unit_price'] = ['nullable', 'numeric', 'min:0'];
        }

        return $request->validate($rules);
    }

    protected function syncServicesForJob(RepairJob $repairJob, array $services): void
    {
        $repairJob->services()->detach();

        if (empty($services)) {
            return;
        }

        $rows = collect($services)->map(function ($service) use ($repairJob) {
            $serviceId = (int) $service['service_id'];
            $quantity = (int) ($service['quantity'] ?? 1);
            $unitPrice = (float) ($service['unit_price'] ?? Service::find($serviceId)?->price ?? 0);
            $subtotal = round($unitPrice * $quantity, 2);

            return [
                'repair_job_id' => $repairJob->id,
                'service_id' => $serviceId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        DB::table('repair_job_services')->insert($rows);
    }
}

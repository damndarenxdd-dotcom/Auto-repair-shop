<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'repair_job_id',
        'customer_id',
        'invoice_number',
        'subtotal',
        'tax',
        'total',
        'status',
        'due_date',
        'paid_date',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function repairJob()
    {
        return $this->belongsTo(RepairJob::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::latest('id')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 3)) + 1 : 1001;
        return 'INV' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'company_name',
        'company_address',
        'kontak',
        'client_name',
        'order_number',
        'payment_method',
        'description',
        'keterangan_tambahan',
        'jenis_bank',
        'kategori_pemasukan',
        'fee_maintenance',
        'subtotal',
        'tax',
        'total',
        'nama_layanan',
        'status_pembayaran'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:0',
        'tax' => 'decimal:0',
        'total' => 'decimal:0',
        'fee_maintenance' => 'decimal:0',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['tax_percentage'];

    // Relationships
    public function kwitansi()
    {
        return $this->hasMany(Kwitansi::class, 'invoice_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'invoice_id');
    }

    public function layanan()
    {
        try {
            return $this->belongsTo(Layanan::class, 'nama_layanan', 'nama_layanan');
        } catch (\Exception $e) {
            Log::error('Error loading layanan: ' . $e->getMessage());
            return null;
        }
    }

    public function perusahaan()
    {
        try {
            return $this->belongsTo(Perusahaan::class, 'company_name', 'nama_perusahaan');
        } catch (\Exception $e) {
            Log::error('Error loading perusahaan: ' . $e->getMessage());
            return null;
        }
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'invoice_id');
    }

    // Accessors
    public function getProjectAttribute()
    {
        return $this->projects()->first();
    }

    public function getTaxPercentageAttribute()
    {
        if ($this->subtotal > 0) {
            return round(($this->tax / $this->subtotal) * 100, 2);
        }
        return 0;
    }

    public function getNamaPerusahaanAttribute()
    {
        return $this->company_name;
    }

    public function getNamaKlienAttribute()
    {
        return $this->client_name;
    }

    public function getAlamatAttribute()
    {
        return $this->company_address;
    }

    public function getPajakAttribute()
    {
        return $this->tax;
    }

    public function getDeskripsiAttribute()
    {
        if (!empty($this->description)) {
            return $this->description;
        }
        
        try {
            $layanan = $this->layanan;
            if ($layanan && !empty($layanan->deskripsi)) {
                return $layanan->deskripsi;
            }
        } catch (\Exception $e) {
            Log::error('Error in getDeskripsiAttribute: ' . $e->getMessage());
        }
        
        return $this->nama_layanan ? 'Layanan: ' . $this->nama_layanan : 'Tidak ada deskripsi';
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::created(function ($invoice) {
            try {
                // Buat project dari invoice
                $invoice->createProjectFromInvoice();
                
                // Buat order otomatis
                $invoice->createOrderFromInvoice();
            } catch (\Exception $e) {
                Log::error('Error in invoice created event: ' . $e->getMessage());
            }
        });

        static::updated(function ($invoice) {
            try {
                // Sinkronisasi project jika ada
                if ($invoice->hasProject()) {
                    $invoice->syncProjectWithInvoice();
                }
            } catch (\Exception $e) {
                Log::error('Error in invoice updated event: ' . $e->getMessage());
            }
        });
    }

    // Methods
    public function createProjectFromInvoice()
    {
        try {
            if ($this->hasProject()) {
                return $this->project;
            }

            $project = Project::create([
                'invoice_id' => $this->id,
                'nama' => $this->nama_layanan ?? 'Project dari Invoice #' . $this->invoice_no,
                'deskripsi' => $this->description ?? '',
                'harga' => $this->total,
                'status_pengerjaan' => 'pending',
                'status_kerjasama' => 'aktif',
                'progres' => 0,
            ]);

            Log::info('Project berhasil dibuat dari invoice', [
                'invoice_id' => $this->id,
                'project_id' => $project->id
            ]);

            return $project;
        } catch (\Exception $e) {
            Log::error('Gagal membuat project dari invoice: ' . $e->getMessage());
            return null;
        }
    }

    public function createOrderFromInvoice()
    {
        try {
            $existingOrder = Order::where('invoice_id', $this->id)->first();
            if ($existingOrder) {
                return $existingOrder;
            }

            $order = Order::create([
                'order_no' => 'ORD-' . $this->id . '-' . time(),
                'layanan' => $this->nama_layanan ?? null,
                'kategori' => $this->nama_layanan ?? null,
                'price' => (int) ($this->subtotal ?? 0),
                'price_formatted' => number_format($this->subtotal ?? 0, 0, ',', '.'),
                'klien' => $this->client_name ?? null,
                'company_name' => $this->company_name ?? null,
                'order_date' => $this->invoice_date ?? null,
                'invoice_no' => $this->invoice_no,
                'company_address' => $this->company_address ?? null,
                'description' => $this->description ?? null,
                'subtotal' => $this->subtotal ?? 0,
                'tax' => $this->tax ?? 0,
                'total' => $this->total ?? 0,
                'payment_method' => $this->payment_method ?? null,
                'deposit' => 0,
                'paid' => 0,
                'status' => 'pending',
                'work_status' => 'planning',
                'invoice_id' => $this->id,
            ]);

            Log::info('Order created from invoice', ['order_id' => $order->id, 'invoice_id' => $this->id]);

            return $order;
        } catch (\Exception $e) {
            Log::error('Failed to create order from invoice: ' . $e->getMessage());
            return null;
        }
    }

    public function syncProjectWithInvoice()
    {
        try {
            $project = $this->project;
            
            if (!$project) {
                return false;
            }

            $project->update([
                'nama' => $this->nama_layanan ?: $project->nama,
                'deskripsi' => $this->description ?: $project->deskripsi,
                'harga' => $this->total ?: $project->harga,
            ]);

            Log::info('Project berhasil disinkronisasi dengan invoice', [
                'invoice_id' => $this->id,
                'project_id' => $project->id
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Gagal sinkronisasi project dengan invoice: ' . $e->getMessage());
            return false;
        }
    }

    public function hasProject()
    {
        return $this->projects()->exists();
    }

    public function getProjectLinkAttribute()
    {
        $project = $this->project;
        if ($project) {
            return route('admin.project.show', $project->id);
        }
        return null;
    }

    public function getProjectStatusAttribute()
    {
        $project = $this->project;
        if ($project) {
            return [
                'status_pengerjaan' => $project->status_pengerjaan ?? 'planning',
                'status_kerjasama' => $project->status_kerjasama ?? 'pending',
                'progres' => ($project->progres ?? 0) . '%'
            ];
        }
        return null;
    }
}

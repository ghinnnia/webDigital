<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasApprovalHistory extends Model
{
    use HasFactory;

    protected $table = 'tugas_approval_history';

    protected $fillable = [
        'tugas_id',
        'approved_by',
        'action',
        'notes',
    ];

    // Relationships
    public function tugas()
    {
        return $this->belongsTo(TugasKaryawanToManager::class, 'tugas_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
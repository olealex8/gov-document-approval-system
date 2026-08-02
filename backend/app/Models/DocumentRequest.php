<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;
    
    protected $fillable = ['applicant_id', 'document_type', 'status', 'decided_by', 'submitted_at', 'decided_at', 'note'];
    protected $casts = ['submitted_at' => 'datetime', 'decided_at' => 'datetime'];

    public function applicant() { return $this->belongsTo(Applicant::class); }
    public function histories() { return $this->hasMany(RequestHistory::class); }
    public function files() { return $this->hasMany(DocumentFile::class); }
    public function decidedBy() { return $this->belongsTo(User::class, 'decided_by'); }
}

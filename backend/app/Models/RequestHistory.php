<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestHistory extends Model
{
    use HasFactory;
    
    protected $fillable = ['document_request_id', 'from_status', 'to_status', 'changed_by', 'note'];

    public function documentRequest() { return $this->belongsTo(DocumentRequest::class); }
    public function changedBy() { return $this->belongsTo(User::class, 'changed_by'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    use HasFactory;
    
    protected $fillable = ['document_request_id', 'original_name', 'path', 'mime_type', 'size'];

    public function documentRequest() { return $this->belongsTo(DocumentRequest::class); }
}

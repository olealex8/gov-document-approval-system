<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'company_name', 'registration_number', 'phone', 'address'];

    public function user() { return $this->belongsTo(User::class); }
    public function documentRequests() { return $this->hasMany(DocumentRequest::class); }
}

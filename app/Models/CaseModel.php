<?php
// app/Models/CaseModel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseModel extends Model
{
    use HasFactory;

    protected $table = 'cases';

    protected $fillable = [
        'case_number',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'created_by',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notes()
    {
        return $this->hasMany(CaseNote::class, 'case_id');
    }

    public function files()
    {
        return $this->hasMany(CaseFile::class, 'case_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($case) {
            if (!$case->case_number) {
                $case->case_number = 'CASE-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}

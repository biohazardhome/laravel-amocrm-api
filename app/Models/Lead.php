<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected 
        $fillable = [
            'name',
            'lead_id',
            'group_id',
            'account_id',
            'pipeline_id',
            'status_id',
            'company_id',
            'price',
        ],
        $casts = [
            'name' => 'string',
            'lead_id' => 'integer',
            'group_id' => 'integer',
            'account_id' => 'integer',
            'pipeline_id' => 'integer',
            'status_id' => 'integer',
            'company_id' => 'integer',
            'price' => 'integer',
        ];

    public static function findByLeadId($id) {
        return Lead::whereLeadId($id)->first();
    }

    public function pipeline() {
        return $this->hasOne(Pipeline::class, 'pipeline_id', 'pipeline_id');
    }

    public function account() {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }

}

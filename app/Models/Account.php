<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected 
        $fillable = [
            'account_id',
            'name',
            'subdomain',
            'country',
            'currency',
        ],
        $casts = [
            'account_id' => 'integer',
            'name' => 'string',
            'subdomain' => 'string',
            'country' => 'string',
            'currency' => 'string',
        ];

    public static function findByAccountId($id) {
        return Account::whereAccountId($id)->first();
    }
}

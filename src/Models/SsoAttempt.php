<?php

namespace Shenasa\Models;

use Illuminate\Database\Eloquent\Model;

class SsoAttempt extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_successful' => 'boolean',
        'expired_at'    => 'datetime',
        'state'         => 'string',
        'confirmed_at'  => 'datetime',
        'user_id'       => 'integer'
    ];
}

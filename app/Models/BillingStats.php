<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingStats extends Model
{
    use HasFactory;
    protected $fillable = ['stats_date', 'stats_month', 'usage_name', 'detail_name', 'subject_type', 'subscriber_name', 'user_name', 'unit_name', 'interface_code', 'hit_type', 'report_count', 'billable'];
}

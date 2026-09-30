<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Application extends Model
{
    use HasApiTokens;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'package_name',
        'platform',
        'description',
        'status',
        'firebase_project_id',
        'api_credentials_metadata',
        'current_version',
        'current_build_number',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'api_credentials_metadata' => 'encrypted:array',
            'current_build_number' => 'integer',
        ];
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}

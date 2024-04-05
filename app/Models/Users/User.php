<?php

namespace App\Models\Users;

use App\Helpers\GeneralHelper;
use Illuminate\Notifications\Notifiable;
use App\Jobs\Users\DecrementDailyRecordJob;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\Users\User
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at

 * @mixin Eloquent
 */

class User extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'gender',
        'name',
        'location',
        'age',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'json',
        'location' => 'json',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $appends = [
        'full_name'
    ];

    /**
     * Full Name
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return GeneralHelper::getFullname(
            titleName: $this->name['title'],
            firstName: $this->name['first'],
            lastName: $this->name['last']
        );
    }
    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($user) {
            DecrementDailyRecordJob::dispatch($user->id)->onQueue('queue_decrement_daily_record');
        });
    }
}

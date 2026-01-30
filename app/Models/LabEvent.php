<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LabEvent extends Model
{
    use HasFactory;
    protected $table = 'lab_events';
    // protected $fillable = [
    //     'title',
    //     'category',
    //     'start',
    //     'end',
    //     'description',
    //     'status',
    //     'user_id',
    // ];
    protected $guarded = [];
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function files()
    {
        return $this->hasMany(LabEventFile::class);
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_code', 'code');
    }

    public function issueRequest()
    {
        return $this->hasOne(\App\Models\EquipmentIssueRequest::class, 'lab_event_id', 'id');
    }


    public function group()
    {
        return $this->belongsTo(Group::class, 'registered_for', 'id');
    }

    public function getDisplayStatusAttribute()
    {
        if ($this->status === 'approved' && Carbon::parse($this->end)->isPast()) {
            return 'completed'; //'Đã hoàn thành'
        }

        return $this->status;
    }

    public function isSeries(): bool
    {
        return !is_null($this->series_id);
    }
    public function eventCategory()
    {
        return $this->hasOne(EventCategory::class, 'code', 'category');
    }

    // Relationship với EventStatus (qua code)
    public function eventStatus()
    {
        return $this->hasOne(EventStatus::class, 'code', 'status');
    }

    // Accessor: lấy icon từ category
    public function getCategoryIconAttribute()
    {
        if ($this->eventCategory) {
            return $this->eventCategory->icon;
        }
        return EventCategory::where('code', $this->category)->value('icon') ?? 'calendar';
    }

    // Accessor: lấy màu từ status
    public function getStatusColorAttribute()
    {
        if ($this->eventStatus) {
            return $this->eventStatus->color;
        }
        return EventStatus::where('code', $this->status)->value('color') ?? '#cccccc';
    }

    // Accessor: lấy tên category
    public function getCategoryNameAttribute()
    {
        return $this->eventCategory?->name ?? ucfirst($this->category);
    }

    // Accessor: lấy tên status
    public function getStatusNameAttribute()
    {
        return $this->eventStatus?->name ?? ucfirst($this->status);
    }

    // Tự động set màu khi tạo/update event
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (!$event->color && $event->status) {
                $event->color = EventStatus::where('code', $event->status)->value('color') ?? '#cccccc';
            }
        });

        static::updating(function ($event) {
            if ($event->isDirty('status')) {
                $event->color = EventStatus::where('code', $event->status)->value('color') ?? '#cccccc';
            }
        });
    }


}

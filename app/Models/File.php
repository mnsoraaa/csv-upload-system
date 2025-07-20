<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'status',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'file_size' => 'integer'
    ];

    /**
     * Check if file is in progress
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if file is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if file failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get human readable file size
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get duration of processing
     */
    public function getProcessingDurationAttribute(): ?string
    {
        if (!$this->started_at) {
            return null;
        }

        $end = $this->completed_at ?? now();
        $duration = $this->started_at->diff($end);

        if ($duration->days > 0) {
            return $duration->format('%d days, %h hours, %i minutes');
        } elseif ($duration->h > 0) {
            return $duration->format('%h hours, %i minutes, %s seconds');
        } elseif ($duration->i > 0) {
            return $duration->format('%i minutes, %s seconds');
        } else {
            return $duration->format('%s seconds');
        }
    }
}

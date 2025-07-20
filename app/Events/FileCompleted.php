<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $fileId;
    public string $status;
    public ?string $message;
    public ?int $processedRecords;

    /**
     * Create a new event instance.
     */
    public function __construct(int $fileId, string $status, ?string $message = null, ?int $processedRecords = null)
    {
        $this->fileId = $fileId;
        $this->status = $status;
        $this->message = $message;
        $this->processedRecords = $processedRecords;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("file.{$this->fileId}"),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'file.completed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'file_id' => $this->fileId,
            'status' => $this->status,
            'message' => $this->message,
            'processed_records' => $this->processedRecords,
            'timestamp' => now()->toISOString(),
        ];
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when user's permissions change
 * Used to clear sidebar menu cache for affected users
 */
class UserPermissionsChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user ID whose permissions changed
     *
     * @var int
     */
    public int $userId;

    /**
     * The role that was added or removed (if applicable)
     *
     * @var string|null
     */
    public ?string $roleName;

    /**
     * The permission that was added or removed (if applicable)
     *
     * @var string|null
     */
    public ?string $permissionName;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param string|null $roleName
     * @param string|null $permissionName
     */
    public function __construct(int $userId, ?string $roleName = null, ?string $permissionName = null)
    {
        $this->userId = $userId;
        $this->roleName = $roleName;
        $this->permissionName = $permissionName;
    }
}
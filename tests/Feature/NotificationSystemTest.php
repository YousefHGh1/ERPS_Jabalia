<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Notification;
use App\Models\Request as RequestModel;
use App\Models\RequestStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $employee;
    protected RequestStatus $completedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->employee = User::factory()->create();
        $this->employee->givePermissionTo(['index notifications', 'read notifications']);

        $this->completedStatus = RequestStatus::factory()->create(['name' => 'مكتمل']);

        Permission::findOrCreate('index notifications');
        Permission::findOrCreate('read notifications');
        Permission::findOrCreate('delete notifications');
    }

    /**
     * Test notification creation when request is completed
     * @test
     */
    public function creates_notification_when_request_is_completed()
    {
        $request = RequestModel::factory()->create();

        $this->actingAs($this->admin);

        $response = $this->put(route('requests.update-status', $request), [
            'status_id' => $this->completedStatus->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'type' => 'request',
            'title' => '!! عاجل - مطلوب إنشاء ملف !!',
            'priority' => 'urgent',
            'icon' => 'fa-exclamation-triangle',
        ]);
    }

    /**
     * Test notification listing and filtering
     * @test
     */
    public function can_list_and_filter_notifications()
    {
        // Create different types of notifications
        Notification::factory()->create([
            'user_id' => $this->employee->id,
            'type' => 'request',
            'priority' => 'urgent',
            'read_at' => null,
        ]);

        Notification::factory()->create([
            'user_id' => $this->employee->id,
            'type' => 'system',
            'priority' => 'normal',
            'read_at' => now(),
        ]);

        Notification::factory()->create([
            'user_id' => $this->employee->id,
            'type' => 'holiday',
            'priority' => 'low',
            'read_at' => null,
        ]);

        $this->actingAs($this->employee);

        // Test listing all notifications
        $response = $this->get(route('notifications.index'));
        $response->assertSuccessful();

        // Test filtering by priority
        $response = $this->get(route('notifications.index', ['priority' => 'urgent']));
        $response->assertSuccessful();

        // Test filtering by type
        $response = $this->get(route('notifications.index', ['type' => 'request']));
        $response->assertSuccessful();

        // Test filtering unread only
        $response = $this->get(route('notifications.index', ['unread' => true]));
        $response->assertSuccessful();
    }

    /**
     * Test marking notification as read
     * @test
     */
    public function can_mark_notification_as_read()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->employee->id,
            'read_at' => null,
        ]);

        $this->actingAs($this->employee);

        $response = $this->post(route('notifications.mark-read', $notification));

        $response->assertJson(['success' => true]);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    /**
     * Test marking all notifications as read
     * @test
     */
    public function can_mark_all_notifications_as_read()
    {
        Notification::factory()->count(5)->create([
            'user_id' => $this->employee->id,
            'read_at' => null,
        ]);

        $this->actingAs($this->employee);

        $response = $this->post(route('notifications.mark-all-read'));

        $response->assertJson(['success' => true]);

        $unreadCount = Notification::where('user_id', $this->employee->id)
            ->whereNull('read_at')
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    /**
     * Test notification deletion
     * @test
     */
    public function can_delete_notification()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->employee->id,
        ]);

        $this->actingAs($this->employee);

        $response = $this->delete(route('notifications.destroy', $notification));

        $response->assertRedirect();

        $this->assertSoftDeleted('notifications', ['id' => $notification->id]);
    }

    /**
     * Test notification permissions
     * @test
     */
    public function notification_permissions_work_correctly()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->employee->id,
        ]);

        // Test unauthorized user cannot access other's notifications
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $response = $this->get(route('notifications.index'));
        $response->assertSuccessful(); // Can see own notifications (empty list)

        $response = $this->post(route('notifications.mark-read', $notification));
        $response->assertForbidden(); // Cannot modify other's notification

        // Test authorized user can access own notifications
        $this->actingAs($this->employee);

        $response = $this->get(route('notifications.index'));
        $response->assertSuccessful();

        $response = $this->post(route('notifications.mark-read', $notification));
        $response->assertJson(['success' => true]);
    }

    /**
     * Test notification statistics
     * @test
     */
    public function can_get_notification_statistics()
    {
        Notification::factory()->count(3)->create([
            'user_id' => $this->employee->id,
            'read_at' => null,
        ]);

        Notification::factory()->count(2)->create([
            'user_id' => $this->employee->id,
            'read_at' => now(),
        ]);

        Notification::factory()->create([
            'user_id' => $this->employee->id,
            'priority' => 'urgent',
            'read_at' => null,
        ]);

        $this->actingAs($this->employee);

        $response = $this->get(route('notifications.stats'));

        $response->assertJson([
            'total' => 6,
            'unread' => 4,
            'urgent_unread' => 1,
        ]);
    }

    /**
     * Test real-time notification updates
     * @test
     */
    public function can_get_real_time_notification_updates()
    {
        Notification::factory()->create([
            'user_id' => $this->employee->id,
            'created_at' => now()->subMinutes(5),
        ]);

        $this->actingAs($this->employee);

        $response = $this->get(route('notifications.recent'));

        $response->assertSuccessful()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'message',
                    'priority',
                    'created_at',
                    'read_at',
                ]
            ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAndMessagingTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Aygul Student',
            'email' => 'student@edu.com',
            'password' => bcrypt('admin123'),
            'role' => 'student',
        ]);

        $this->teacher = User::create([
            'name' => 'Azamat Teacher',
            'email' => 'teacher@edu.com',
            'password' => bcrypt('admin123'),
            'role' => 'teacher',
        ]);
    }

    public function test_user_can_access_notifications_page(): void
    {
        $response = $this->actingAs($this->student)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Bildirimler');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $notif = Notification::create([
            'user_id' => $this->student->id,
            'type' => 'comment',
            'title' => 'Test Notification',
            'body' => 'This is a test notification',
            'url' => '/dashboard',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->student)->get(route('notifications.read', $notif->id));
        $response->assertRedirect('/dashboard');

        $this->assertTrue($notif->fresh()->is_read);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        Notification::create([
            'user_id' => $this->student->id,
            'type' => 'comment',
            'title' => 'Notification 1',
            'body' => 'Body 1',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $this->student->id,
            'type' => 'message',
            'title' => 'Notification 2',
            'body' => 'Body 2',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->student)->post(route('notifications.mark-all-read'));
        $response->assertRedirect();

        $unreadCount = Notification::where('user_id', $this->student->id)->where('is_read', false)->count();
        $this->assertEquals(0, $unreadCount);
    }

    public function test_user_can_clear_all_notifications(): void
    {
        Notification::create([
            'user_id' => $this->student->id,
            'type' => 'comment',
            'title' => 'Notification 1',
            'body' => 'Body 1',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $this->student->id,
            'type' => 'message',
            'title' => 'Notification 2',
            'body' => 'Body 2',
            'is_read' => true,
        ]);

        $response = $this->actingAs($this->student)->delete(route('notifications.clear-all'));
        $response->assertRedirect();

        $totalCount = Notification::where('user_id', $this->student->id)->count();
        $this->assertEquals(0, $totalCount);
    }

    public function test_user_can_delete_notification(): void
    {
        $notif = Notification::create([
            'user_id' => $this->student->id,
            'type' => 'comment',
            'title' => 'Notification to delete',
            'body' => 'Body',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->student)->delete(route('notifications.destroy', $notif->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('notifications', ['id' => $notif->id]);
    }

    public function test_user_can_access_messages_page(): void
    {
        $response = $this->actingAs($this->student)->get(route('messages.index'));
        $response->assertStatus(200);
        $response->assertSee('Mesajlar');
    }

    public function test_user_can_send_message(): void
    {
        $data = [
            'receiver_id' => $this->teacher->id,
            'body' => 'Teacher, I need help with lesson 3.',
        ];

        $response = $this->actingAs($this->student)->post(route('messages.store'), $data);
        $response->assertRedirect();

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->student->id,
            'receiver_id' => $this->teacher->id,
            'subject' => 'Chat Message',
            'body' => 'Teacher, I need help with lesson 3.',
        ]);
    }

    public function test_user_can_delete_message(): void
    {
        $message = Message::create([
            'sender_id' => $this->student->id,
            'receiver_id' => $this->teacher->id,
            'subject' => 'Chat Message',
            'body' => 'Some text',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->student)->delete(route('messages.destroy', $message->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }

    public function test_unread_messages_count_is_correctly_displayed_on_nav_and_badge(): void
    {
        // Initially, student has 0 unread messages
        $response = $this->actingAs($this->student)->get(route('messages.index'));
        $response->assertStatus(200);
        $response->assertDontSee('class="badge-unread"', false);

        // Receive 2 messages from teacher
        Message::create([
            'sender_id' => $this->teacher->id,
            'receiver_id' => $this->student->id,
            'subject' => 'Hello',
            'body' => 'First message',
            'is_read' => false,
        ]);
        Message::create([
            'sender_id' => $this->teacher->id,
            'receiver_id' => $this->student->id,
            'subject' => 'Hello 2',
            'body' => 'Second message',
            'is_read' => false,
        ]);

        // Student now has 2 unread messages, which should render in the nav badge
        $response = $this->actingAs($this->student)->get(route('messages.index'));
        $response->assertStatus(200);
        $response->assertSee('badge-unread', false);
        $response->assertSee('2');
    }
}
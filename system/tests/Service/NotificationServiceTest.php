<?php

namespace App\Tests\Service;

use App\Service\NotificationService;
use App\Entity\User;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class NotificationServiceTest extends TestCase
{
    public function testCreateNotification(): void
    {
        // Mock EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        $service = new NotificationService($entityManager);

        // Mock User
        $user = new User();
        
        $notification = $service->create(
            $user,
            'info',
            'Test Notification',
            'This is a test message'
        );

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals('info', $notification->getType());
        $this->assertEquals('Test Notification', $notification->getTitle());
        $this->assertEquals('This is a test message', $notification->getMessage());
        $this->assertFalse($notification->isRead());
    }

    public function testMarkAsRead(): void
    {
        $notification = new Notification();
        $notification->setIsRead(false);

        $this->assertFalse($notification->isRead());

        $notification->setIsRead(true);

        $this->assertTrue($notification->isRead());
    }
}

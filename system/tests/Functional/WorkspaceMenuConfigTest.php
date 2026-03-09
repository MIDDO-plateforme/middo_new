<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WorkspaceMenuConfigTest extends KernelTestCase
{
    public function testConfigIsLoaded(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $config = $container->getParameter('workspace_menu10');

        $this->assertIsArray($config);
        $this->assertTrue($config['enabled']);
        $this->assertSame('/api/workspaces', $config['api_prefix']);
        $this->assertContains('health', $config['features']);
    }
}

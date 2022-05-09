<?php

namespace OCA\Schulcloud\Tests\Integration;

use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

use OCA\Schulcloud\AppInfo\Application;

class AppTest extends TestCase {

    private $app;
    private $container;

    public function setUp(): void{
        parent::setUp();
        $this->app = new Application();

        /*
        $bootContext = $this->createMock(IBootContext::class);
        $registrationContext = $this->createMock(IRegistrationContext::class);
        $this->app->register($registrationContext);
        $this->app->boot($bootContext);
        */

        $this->container = $this->app->getContainer();
    }

    public function testAppInstalled() {
        $appManager = $this->container->get('OCP\App\IAppManager');
        $this->assertTrue($appManager->isInstalled('schulcloud'));
    }
}

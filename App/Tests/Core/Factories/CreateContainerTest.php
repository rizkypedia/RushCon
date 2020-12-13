<?php


use PHPUnit\Framework\TestCase;
use Rushcon\Core\Container;
use Rushcon\Core\Factories\ContainerFactory;

class CreateContainerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testCreateContainerisValidObject() {
        $container = new Container();
        $createFromFactory = ContainerFactory::create();
        $this->assertEquals($container, $createFromFactory);
    }

}
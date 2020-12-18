<?php


namespace Rushcon\custom\plugins\BasicCalculator\Controllers;


use Rushcon\Core\BaseController;
use Rushcon\Core\Console;
use Rushcon\Core\Container;

class CalculatorController extends BaseController
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * @param int $left
     * @param int $right
     */
    public function addAction(int $left, int $right):void {
        Console::pprintln($left+$right);
    }

    /**
     * @param int $left
     * @param int $right
     */
    public function multiplyAction(int $left, int $right):void {
        Console::pprintln($left*$right);
    }
}

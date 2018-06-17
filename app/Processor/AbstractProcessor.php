<?php
declare(strict_types=1);

namespace App\Processor;

abstract class AbstractProcessor
{
    abstract public function process(): void;
}
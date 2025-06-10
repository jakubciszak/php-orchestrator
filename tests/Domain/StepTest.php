<?php

declare(strict_types=1);

namespace JakubCiszak\Orchestrator\Tests\Domain;

use JakubCiszak\Orchestrator\Domain\{DomainEvent, NextStepStrategyInterface, ProcessState, Step};
use PHPUnit\Framework\TestCase;

final class StepTest extends TestCase
{
    public function testNextDelegatesDecisionToStrategy(): void
    {
        $strategy = new class() implements NextStepStrategyInterface {
            public function decide(DomainEvent $event, ProcessState $state): ?string
            {
                return 'next_step';
            }
        };

        $step = new Step('step1', 'SomeCommand', 'SomeEvent', $strategy);

        $event = new class() implements DomainEvent {};
        $state = new ProcessState('proc1', 'step1');

        $this->assertSame('next_step', $step->next($event, $state));
    }
}

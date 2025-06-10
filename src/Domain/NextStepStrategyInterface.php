<?php

declare(strict_types=1);

namespace JakubCiszak\Orchestrator\Domain;

interface NextStepStrategyInterface
{
    public function decide(DomainEvent $event, ProcessState $state): ?string;
}

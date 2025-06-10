<?php

declare(strict_types=1);

namespace JakubCiszak\Orchestrator\Domain;

final readonly class Step
{
    public function __construct(
        private string $id,
        private string $commandFqcn,
        private string $expectedEventFqcn,
        private NextStepStrategyInterface $nextStepStrategy,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCommandFqcn(): string
    {
        return $this->commandFqcn;
    }

    public function getExpectedEventFqcn(): string
    {
        return $this->expectedEventFqcn;
    }

    public function next(DomainEvent $event, ProcessState $state): ?string
    {
        return $this->nextStepStrategy->decide($event, $state);
    }
}

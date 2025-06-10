<?php

declare(strict_types=1);

namespace JakubCiszak\Orchestrator\Domain;

final class ProcessState
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private readonly string $processId,
        private string $currentStepId,
        private array $metadata = [],
    ) {
    }

    public function getProcessId(): string
    {
        return $this->processId;
    }

    public function getCurrentStepId(): string
    {
        return $this->currentStepId;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function withCurrentStepId(string $currentStepId): self
    {
        $clone = clone $this;
        $clone->currentStepId = $currentStepId;

        return $clone;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public function withMetadata(array $metadata): self
    {
        $clone = clone $this;
        $clone->metadata = $metadata;

        return $clone;
    }
}

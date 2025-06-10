# AGENTS.md

> **Project • PHP Generic Orchestrator – guidelines for all Codex agents**

This library must be **100 % domain‑agnostic** (no onboarding/KYC specifics).
It will be published on Packagist; users install it via Composer and wire their own steps and strategies.

---

## 0. Project overview

The library provides a **workflow orchestration engine** based on DDD principles. It:

* publishes and consumes **domain events**
* dispatches **commands** to external bounded contexts
* drives the flow via configurable **Steps** and **Strategies**.

The engine **never contains business rules** – only the mechanics of process control.

---

## 1. DDD architecture (the domain is “workflow” itself)

| Layer              | What it holds                                                                   | Purity rules                                                                    |
| ------------------ | ------------------------------------------------------------------------------- | ------------------------------------------------------------------------------- |
| **Domain**         | `Step`, `NextStepStrategyInterface`, aggregate `ProcessState`, `ProcessManager` | • Zero framework deps.<br>• Neutral naming (“Process”, “Step”).                 |
| **Application**    | Ports/adapters to buses, glue to frameworks                                     | • Implement interfaces, keep them DI‑friendly.<br>• No business branching here. |
| **Infrastructure** | Symfony CompilerPass, DI tags, adapters for PSR buses                           | • Anything framework‑specific lives here so Domain stays portable.              |

---

## 2. Declaration beats implementation

* Workflow steps are **instances** of the `Step` class registered via `orchestrator.step` tag.
* "What’s next?" logic is encapsulated in classes implementing `NextStepStrategyInterface`.
* Users describe their flow in `services.yaml`; **core code never changes**.

---

## 3. DDD & quality checklist

1. **Events/Commands** – names belong to the host app; the library only routes objects.
2. **Aggregates** – the only built‑in aggregate is `ProcessState` (id, currentStep, meta).
3. **Idempotency** – engine exposes a hook for processed‑event registry; storage implementation is on the host side.
4. **Extensibility** – new step = new service definition; new rule = new Strategy class. Core remains untouched.
5. **Framework‑agnostic** – Core depends exclusively on PSR interfaces; Symfony/Laravel adapters live in separate packages.

---

## 4. Test Driven Development

* **Every feature starts with a failing test** – then implementation.
* Code coverage target **≥ 90 %** for the `core/` package (unit + integration).
* Unit tests: `ProcessManager`, `StepRegistry`, sample strategies.
* Integration tests: full “happy path” using in‑memory buses (see `/tests/Scenario/`).
* External adapters (Symfony/Laravel) hold their own tests and may mock framework classes.

---

## 5. Glossary

| Term               | Meaning inside the library                                                     |
| ------------------ | ------------------------------------------------------------------------------ |
| **Step**           | Configurable stage of a workflow, instance of `Step`.                          |
| **Strategy**       | Decides which step comes next, implements `NextStepStrategyInterface`.         |
| **ProcessManager** | The conductor – reacts to events, mutates `ProcessState`, dispatches commands. |
| **ProcessState**   | Aggregate snapshot (generic, no domain fields).                                |

---

## 6. Style & CI

* Target PHP 8.3+, PSR‑12 code style.
* Static analysis: Psalm level 1 & PHPStan max.
* GitHub Actions: `composer validate`, `phpunit --coverage`, `psalm`, `php-cs-fixer`.

> **Prime directive** – the engine knows nothing about your business; you feed it commands and events, it moves the process forward.

Happy shipping! 🚢

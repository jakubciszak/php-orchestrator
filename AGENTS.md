# AGENTS.md

> **Projekt • Onboarding Orchestrator – wytyczne dla wszystkich agentów Codex**

---

## 0. Ogólny opis projektu

Budujemy moduł **Orchestrator** odpowiadający za sterowanie całym procesem onboardingu klienta w fintechu.
*Sterowanie* = ustalanie kolejnych kroków (**Step**) i emitowanie komend do wyspecjalizowanych modułów (Questions, Documents, Risk …).
Orchestrator **nie wykonuje** logiki biznesowej kroków – jedynie nimi zarządza.

---

## 1. Zawsze myśl w DDD i rozdzielaj konteksty

| Zasada                  | Co to znaczy w repo?                                                                                                                                                                              |
| ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Bounded Context**     | • Orchestrator trzyma własny kod, własne eventy i własną bazę (snapshot procesu).<br>• Każdy krok (Questions, Risk, …) to odrębny pakiet / kontener, kontakt tylko przez *komendy* i *zdarzenia*. |
| **Ubiquitous Language** | Nazwy klas/plików/opisów w README muszą odpowiadać słownikowi biznesu (Step, RiskCalculated, Strategy …)                                                                                          |
| **Aggregates**          | Jedyny agregat w Orchestratorze to `OnboardingState`. Trzymaj go małym: `stage`, `risk`, `attempts`, itp.                                                                                         |

---

## 2. Deklaracja > implementacja

* **Konfiguracja kieruje ruchem** – flow kroków opisujemy w DI (`services.yaml`) przez instancje `Step`.
* **Strategie decyzyjne** (co dalej?) są wstrzykiwanymi serwisami implementującymi `NextStepStrategyInterface`.

  * Dodajesz nowy krok? ➜ dopisujesz definicję w YAML, nie zmieniasz ProcessManagera.
* **Kod silnika** (`ProcessManager`, `Step`, `StepRegistry`) jest *stabilny* – nie dopuszczamy reguł biznesowych wewnątrz.

---

## 3. “Zawsze stosuj DDD” — konkretne check‑listy

1. **Nazewnictwo**

   * Event = „co SIĘ stało” → `DocsCollected`, `RiskCalculated`.
   * Command = „zrób” → `CollectDocs`, `CalculateRisk`.
2. **Granice (moduły)**

   * Orchestrator publikuje *tylko* komendy/eventy; nie ładuje repozytoriów innych kontekstów.
3. **Brak lepkiego stanu**

   * `ProcessManager` wczytuje `OnboardingState`, podejmuje decyzję, zapisuje, wysyła komendę – koniec transakcji.
4. **Idempotencja**

   * Listener zdarzenia *musi* sprawdzić, czy event już obsłużono (kolumna `last_event_id`).
5. **Testy**

   * Jednostkowe: strategia + step registry.
   * Integracyjne: scenariusz “happy path” symulowany zdarzeniami (patrz `/tests/Scenario/`).

---

## 4. Test Driven Development

* **Nowa funkcjonalność zaczyna się od testu** – pisz failing test, dopiero potem implementację.
* Pokrycie testami (unit + integration) min. **80 %**; w CI build zostanie zablokowany, jeśli spadnie niżej.
* Mocki *tylko* na granicach kontekstów; wewnętrzne klasy testujemy realnie.
* Scenariusze end‑to‑end (Behat/Cypress) dla krytycznych ścieżek biznesowych: start → finish oraz happy‑path z manual‑check.

> **Pamiętaj** – jeśli coś zaczyna pachnieć „if‑else spaghetti” w ProcessManagerze, wróć do punktu 2: przenieś regułę do **Strategii** lub konfiguracji DI.

---

## Słowniczek skrótów

| Termin              | Znaczenie w projekcie                                                          |
| ------------------- | ------------------------------------------------------------------------------ |
| **Step**            | Konfigurowalny etap procesu, opisany serwisem Symfony (klasa `Step`).          |
| **Strategy**        | Serwis decydujący o następnym kroku, implementuje `NextStepStrategyInterface`. |
| **ProcessManager**  | Jedyny „dyrygent” – reaguje na zdarzenia, zmienia stan, wysyła komendy.        |
| **OnboardingState** | Agregat, snapshot gdzie klient się znajduje.                                   |

---

Happy coding! ✌️

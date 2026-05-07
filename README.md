# Quotes Explorer Package 🚀

A modular Laravel package for optimized management of famous quotes using the DummyJSON API. This project demonstrates advanced skills in algorithms, service resilience, package architecture, and modern frontend development.

## 🛠️ Technology Stack
- **Backend:** Laravel Package, PestPHP (tests).
- **Frontend:** Vue 3 (Composition API), TypeScript, Vite.
- **Infrastructure:** Docker (self-contained environment).

## 🚀 Installation and Evaluation (Single Command)

According to the task requirements, the project is orchestrated to start with a single command. Follow these steps:

1. **Clone the repository.**
2. **Run Docker Compose:**
   ```bash
   docker-compose up -d
   ```
3. **Access the application:**

   By default at [http://localhost:8080/](http://localhost:8080/)
   If there are issues, visit [http://localhost:8080/quotes-app](http://localhost:8080/quotes-app) in your browser.

4. **Initial Import (CLI):**
   To populate the initial cache and test pagination, run the command inside the container:
   ```bash
   docker exec -it quotes-package-env php artisan quotes:batch-import 100
   ```

## 🏗️ Package Architecture

- **Search Algorithm:** Manual implementation of **Binary Search $O(\log n)$** over sequentially indexed arrays, meeting the restriction of not using ID keys.
- **Resilience:** Non-blocking `RateLimiterService` that throws 429 exceptions. The CLI command catches these exceptions and performs intelligent waiting (`sleep`) before retrying.
- **Frontend:** Embedded SPA built with **Vue 3 and TypeScript**. Assets are precompiled and integrated through Laravel's publishing system.
- **Code Quality (QA):** Test suite with 19 tests (Unit and Feature) covering business logic.

## 📁 Main Structure
```
src/
├── QuotesManager.php             # Orchestrator: Cache + Binary Search
├── Services/
│   ├── BinarySearchService.php   # Pure algorithm logic
│   ├── QuoteApiClient.php        # Resilient HTTP client
│   └── RateLimiterService.php    # Rate limiting control (throttle)
resources/js/
├── components/                   # Modular UI (Card, Search, Pagination)
├── App.vue                       # Frontend orchestrator
└── types.ts                      # Strict TypeScript definitions
tests/
├── Unit/                         # Algorithm and services in isolation
└── Feature/                      # Integration, CLI, and pagination
```
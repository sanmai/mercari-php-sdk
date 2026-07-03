# AI Agent Guidelines

This is a community-maintained PHP client for the Mercari API.

It is designed to be type-safe and easy to use, providing a structured way to interact with Mercari's endpoints.

- **PHP Version:** 8.2 or newer.
- **Core Architecture:**
    - **Clients:** `MercariAuthClient` (for OAuth 2 tokens) and `MercariClient` (for API requests).
    - **Requests:** Typed objects (e.g., `SearchRequest`) that encapsulate request parameters.
    - **Responses/DTOs:** Typed objects returned by the clients. List responses are both `IteratorAggregate` and `Countable`.
- **End-user documentation:** @README.md

## Project Navigation

**Where to look:**
- **Core Logic:** @src/MercariClient.php (the main entry point for all API calls).
- **Data Models:** `src/DTO/` (all response objects and API entities).
- **Request Definitions:** `src/` (classes ending in `Request`, e.g., `SearchRequest.php`).
- **Tests:** `tests/` (mirroring the `src/` structure).

## Coding Standards

- **Type Hinting:** Use precise type hints for parameters and return types. Use generics (`@template`) where appropriate for list responses.
- **DTOs:** Data Transfer Objects should be simple classes with public properties.
- **Naming:** Follow PSR-12 coding standards.

## Implementation Details

- **`postFallback()`**: Used for endpoints that may return an error HTTP status but a success payload (indicated by a `Failure` code of 0).
- **`getOptional()`**: Allows swallowing specific HTTP status codes (e.g., 400, 404) to return `null` instead of throwing.
- **`#[PostDeserialize]`**: Use this annotation for normalizing DTO properties after JMS deserialization.
- **Pagination**: Endpoints use either `page` (zero-indexed offset) or `page_token` (cursor), depending on the API specification.

## Development Workflow

1. **Code Standardization**: Always run `make cs` before submitting changes for review. This ensures style compliance (PER-CS), applies modern PHP 8.2 standards, removes unused imports, and maintains project-wide structural consistency (including license headers).
2. **Full Verification**: Run `make test` to execute the complete validation pipeline. This typically includes:
    - Coding style and linting.
    - Static analysis.
    - Unit and functional tests.
    - Mutation testing.
    - Package and configuration validation.
    *Refer to the `Makefile` for the exact tools and current configurations.*
3. **Parallel Execution**: Use `make -j -k` to run checks in parallel and identify all failures at once.
    - To run a single test file while iterating: `vendor/bin/phpunit tests/SpecificTest.php`.
4. **Testing Requirement**: Every new endpoint or bug fix must be accompanied by a corresponding test in the `tests/` directory.
5. **Mocking**: Use the patterns established in existing tests for mocking API responses.

## Documentation Style

- **No Hard-Wrapped Lines:** Write each paragraph as a single long line in Markdown files. Let the editor handle soft-wrapping.
- **Clarity:** Keep documentation concise and focused on usage examples.

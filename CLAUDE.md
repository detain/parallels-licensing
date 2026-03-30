# parallels-licensing

PHP client library for the Parallels Key Administrator (KA) API. Wraps XML-RPC2 calls to `https://ka.parallels.com:7050/` and `https://kademo.parallels.com:7050/`.

## Commands

```bash
composer install                          # install deps (requires ext-soap, pear/xml_rpc2)
vendor/bin/phpunit tests/ -v   # run all tests
phpdbg -qrr vendor/bin/phpunit tests/ -v --coverage-clover coverage.xml --whitelist src/  # with coverage
```

```bash
# Static analysis via Scrutinizer (see .scrutinizer.yml for config)
php -l src/Parallels.php          # syntax check main library
php -l tests/ParallelsTest.php    # syntax check tests
```

```bash
# Generate clover coverage report
phpdbg -qrr vendor/bin/phpunit tests/ -v --coverage-clover coverage.xml --whitelist src/
# View coverage summary
vendor/bin/phpunit tests/ --coverage-text --whitelist src/
```

## Architecture

- **Library class**: `src/Parallels.php` · namespace `Detain\Parallels\` · PSR-4 via `composer.json`
- **Tests**: `tests/ParallelsTest.php` · extends `PHPUnit\Framework\TestCase` · instantiates `Parallels()` in `setUp()`
- **XML-RPC client**: `XML_RPC2_Client::create($url, $xmlOptions)` from `pear/xml_rpc2` · stored as `$this->xml`
- **API endpoints**: production `https://ka.parallels.com:7050/` · demo `https://kademo.parallels.com:7050/`
- **Credentials**: via constructor args or constants `PARALLELS_KA_LOGIN`, `PARALLELS_KA_PASSWORD`, `PARALLELS_KA_CLIENT`
- **IDE config**: `.idea/` contains PhpStorm project settings including `inspectionProfiles/`, `deployment.xml` (remote server mappings), and `encodings.xml` (file encoding config)

## Key Methods

- `authInfo()` → `['login' => ..., 'password' => ...]`
- `serverAddress($ips, $macs)` → address struct for API calls
- `createKey()`, `retrieveKey()`, `activateKey()`, `terminateKey()`, `resetKey()`
- `getKeyInfo()`, `getKeysInfoByIP()`, `getMainKeyFromIp()`, `getKeyNumbers()`
- `getAvailableUpgrades()`, `getAvailableKeyTypesAndFeatures()`
- `addNoteToKey()`, `sendKeyByEmail()`

## Conventions

- Tabs for indentation (enforced by `.scrutinizer.yml`)
- camelCase for properties and parameters
- UPPERCASE constants (`PARALLELS_KA_LOGIN`, `PARALLELS_KA_PASSWORD`, `PARALLELS_KA_CLIENT`)
- PHPDoc blocks on every method with `@param` and `@return`
- `$xmlOptions` array: `['sslverify' => false]` default; pass override in constructor
- `$licenseType`: `'billing'` or `'purchase'`
- Optional `StatisticClient` integration: loaded only if `workerman/statistics` path exists

## Testing

- Test class: `tests/ParallelsTest.php` · `setUp()` creates `new Parallels()`
- Live API tests require `PARALLELS_KA_LOGIN` / `PARALLELS_KA_PASSWORD` constants set
- Most tests currently use `markTestIncomplete()` — implement by calling `$this->object->methodName()`
- Coverage target: `--whitelist src/` against `coverage.xml` in clover format

## CI / Static Analysis

- `.travis.yml`: PHP 5.4–7.1 matrix; coverage only on 7.0 via `phpdbg`
- `.scrutinizer.yml`: PHPUnit + clover coverage; excludes `tests/*` from analysis
- `.codeclimate.yml`: phpmd enabled; cyclomatic complexity threshold 100; excludes `tests/`
- `.bettercodehub.yml`: PHP language configured
- Packagist: `detain/parallels-licensing` · LGPL-2.1-only

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->

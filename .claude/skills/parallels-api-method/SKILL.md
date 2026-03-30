---
name: parallels-api-method
description: Adds a new method to src/Parallels.php following the XML-RPC2 call pattern. Use when user says 'add API method', 'implement KA endpoint', 'new Parallels call', or needs to wrap a KA API function. Key capabilities: StatisticClient tick/report integration, $this->xml->__call with authInfo(), $this->response storage. Do NOT use for modifying existing methods or changing constructor/auth logic.
---
# parallels-api-method

## Critical

- **Never** call `$this->xml->methodName()` directly — always use `$this->xml->__call('partner10.methodName', [...])`
- **Always** pass `$this->authInfo()` as the first element of the args array
- **Always** assign the result to `$this->response` and return it — never return the `__call` result directly
- **Always** wrap `StatisticClient` calls with `class_exists(\StatisticClient::class, false)` guards — the class may not be loaded
- Use tabs for indentation (enforced by `.scrutinizer.yml`)
- PHPDoc block required on every method with `@param` and `@return` tags

## Instructions

1. **Identify the KA API method name.** Parallels KA methods follow the pattern `partner10.methodName` (e.g., `partner10.terminateKey`, `partner10.getKeyInfo`). Confirm the exact string from the KA API docs or existing calls in `src/Parallels.php`.

2. **Define the PHP method signature** in `src/Parallels.php` inside the `Parallels` class. Use camelCase, add a PHPDoc block:
   ```php
   /**
    * @param string $key
    * @param string $note
    * @return mixed
    */
   public function myNewMethod($key, $note)
   {
   ```
   Verify the method name does not already exist in `src/Parallels.php` before proceeding.

3. **Add the StatisticClient tick** as the first statement inside the method body:
   ```php
   if (class_exists(\StatisticClient::class, false)) {
       \StatisticClient::tick('Parallels', 'myNewMethod');
   }
   ```

4. **Make the XML-RPC call**, assigning to `$this->response`:
   ```php
   $this->response = $this->xml->__call('partner10.myNewMethod', [$this->authInfo(), $key, $note]);
   ```
   - First arg after `authInfo()` comes from the method parameters
   - If the method needs `serverAddress()`, pass `$this->serverAddress($ips, $macs)` as the second arg (see `createKey()` for reference)
   - If the method needs `$this->client` or `$this->licenseType`, use those properties with `=== false` fallback pattern from `createKey()`

5. **Add the StatisticClient report** block after the call:
   ```php
   if ($this->response === false) {
       if (class_exists(\StatisticClient::class, false)) {
           \StatisticClient::report('Parallels', 'myNewMethod', false, 1, 'XML Call Error', STATISTICS_SERVER);
       }
   } else {
       if (class_exists(\StatisticClient::class, false)) {
           \StatisticClient::report('Parallels', 'myNewMethod', true, 0, '', STATISTICS_SERVER);
       }
   }
   ```

6. **Return `$this->response`:**
   ```php
   return $this->response;
   }
   ```

7. **Add a stub test** in `tests/ParallelsTest.php` following the existing pattern:
   ```php
   /**
    * @covers Detain\Parallels\Parallels::myNewMethod
    * @todo   Implement testMyNewMethod().
    */
   public function testMyNewMethod()
   {
       $this->markTestIncomplete(
           'This test has not been implemented yet.'
       );
   }
   ```

8. **Run the test suite** to confirm no regressions:
   ```bash
   vendor/bin/phpunit tests/ -v
   ```

## Examples

**User says:** "Add a `getKeyStatus` method that calls `partner10.getKeyStatus` with a key number"

**Actions taken:**
1. Confirmed `getKeyStatus` does not exist in `src/Parallels.php`
2. Added after the last method before the closing `}`:

```php
/**
 * @param string $key
 * @return mixed
 */
public function getKeyStatus($key)
{
    if (class_exists(\StatisticClient::class, false)) {
        \StatisticClient::tick('Parallels', 'getKeyStatus');
    }
    $this->response = $this->xml->__call('partner10.getKeyStatus', [$this->authInfo(), $key]);
    if ($this->response === false) {
        if (class_exists(\StatisticClient::class, false)) {
            \StatisticClient::report('Parallels', 'getKeyStatus', false, 1, 'XML Call Error', STATISTICS_SERVER);
        }
    } else {
        if (class_exists(\StatisticClient::class, false)) {
            \StatisticClient::report('Parallels', 'getKeyStatus', true, 0, '', STATISTICS_SERVER);
        }
    }
    return $this->response;
}
```

3. Added stub test in `tests/ParallelsTest.php`
4. Ran `vendor/bin/phpunit tests/ -v` — all tests passed

**Result:** New method consistent with `terminateKey()`, `resetKey()`, `activateKey()` pattern.

## Common Issues

- **`XML_RPC2_Exception: SSL certificate verify failed`** — `$xmlOptions` must include `'sslverify' => false`. This is the default in the constructor; if you're instantiating `Parallels` manually, do not pass an empty `$xmlOptions` array.
- **`Call to undefined constant STATISTICS_SERVER`** — only fires if `StatisticClient` is loaded but `STATISTICS_SERVER` is not defined. Ensure the `class_exists(\StatisticClient::class, false)` guard is present on every `StatisticClient::report()` call.
- **`Fatal error: Class 'XML_RPC2_Client' not found`** — `pear/xml_rpc2` is not installed. Run `composer install` and confirm `ext-soap` is enabled in `php -m`.
- **`PHP_EOL` / indentation errors in `.scrutinizer.yml` report** — file uses tabs, not spaces. Check your editor is not converting tabs to spaces on save.

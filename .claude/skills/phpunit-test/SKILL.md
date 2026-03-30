---
name: phpunit-test
description: Adds a PHPUnit test case to `tests/ParallelsTest.php` for a method in `src/Parallels.php`. Use when user says 'write test', 'add test for', 'implement test', or wants to complete a `markTestIncomplete` stub. Key: use `$this->object->methodName()` on the shared `$object` instance. Do NOT use for integration/live API tests without credentials set.
---
# phpunit-test

## Critical

- ALL tests live in `tests/ParallelsTest.php` — never create a new test file.
- Always use `$this->object` (set in `setUp()` as `new Parallels()`) — never instantiate `Parallels` inside a test method.
- Tests that call live XML-RPC methods require `PARALLELS_KA_LOGIN` / `PARALLELS_KA_PASSWORD` constants. Without them, add a `markTestSkipped` guard instead of `markTestIncomplete`.
- Remove the `markTestIncomplete(...)` call and `@todo` annotation when implementing a stub — do not leave both in place.
- Method naming convention: `test` + exact method name preserving its casing (e.g. `testserverAddress`, `testGetKeyInfo`).

## Instructions

1. **Read the target method signature** in `src/Parallels.php`. Note its parameters, return type (from PHPDoc `@return`), and whether it makes a live XML-RPC call via `$this->xml->__call(...)`. Verify the method exists before proceeding.

2. **Locate the existing stub** in `tests/ParallelsTest.php` matching the method. The stub looks like:
   ```php
   /**
    * @covers Detain\Parallels\Parallels::methodName
    * @todo   Implement testMethodName().
    */
   public function testMethodName()
   {
       // Remove the following lines when you implement this test.
       $this->markTestIncomplete(
           'This test has not been implemented yet.'
       );
   }
   ```
   Verify the stub is present. If the method has no stub, add one following the pattern above before implementing it.

3. **Determine the test strategy** based on the method type:
   - **Pure/local methods** (no `$this->xml` call, e.g. `authInfo`, `serverAddress`): assert return shape directly.
   - **XML-RPC methods** (call `$this->xml->__call(...)`): guard with a credentials check, then assert the response is not `false`.

4. **Replace the stub body**. Remove `@todo` from PHPDoc and delete the `markTestIncomplete` block. Use `$this->object->methodName(args)` and PHPUnit assertion methods (`assertTrue`, `assertIsArray`, `assertNotFalse`, `assertArrayHasKey`).

   For **pure methods**:
   ```php
   /**
    * @covers Detain\Parallels\Parallels::serverAddress
    */
   public function testserverAddress()
   {
       $addr = $this->object->serverAddress(['1.2.3.4'], ['AA:BB:CC:DD:EE:FF']);
       $this->assertTrue(is_array($addr), 'returned proper type');
       $this->assertTrue(array_key_exists('ips', $addr), 'returned required fields');
       $this->assertTrue(array_key_exists('macs', $addr), 'returned required fields');
   }
   ```

   For **XML-RPC methods**:
   ```php
   /**
    * @covers Detain\Parallels\Parallels::getKeyInfo
    */
   public function testGetKeyInfo()
   {
       if (!defined('PARALLELS_KA_LOGIN') || !defined('PARALLELS_KA_PASSWORD')) {
           $this->markTestSkipped('Live API credentials not configured.');
       }
       $result = $this->object->getKeyInfo('SAMPLE-KEY-NUMBER');
       $this->assertNotFalse($result, 'API call should not return false');
   }
   ```

5. **Run the tests** to confirm no regressions:
   ```bash
   vendor/bin/phpunit tests/ -v
   ```
   Verify your new test shows as passed (or skipped if credentials are absent) and no previously passing tests are broken.

## Examples

**User says:** "implement the test for serverAddress"

**Actions taken:**
1. Read `src/Parallels.php` — `serverAddress($ips, $macs)` returns an array with keys `ips` and `macs`, no XML-RPC call.
2. Located stub `testserverAddress()` in `tests/ParallelsTest.php`.
3. Replaced `markTestIncomplete` body with array-shape assertions using `$this->object->serverAddress([...], [...])`.
4. Ran `vendor/bin/phpunit tests/ -v` — test passes.

**Result:**
```php
/**
 * @covers Detain\Parallels\Parallels::serverAddress
 */
public function testserverAddress()
{
    $addr = $this->object->serverAddress(['1.2.3.4'], ['AA:BB:CC:DD:EE:FF']);
    $this->assertTrue(is_array($addr), 'returned proper type');
    $this->assertTrue(array_key_exists('ips', $addr), 'returned required fields');
    $this->assertTrue(array_key_exists('macs', $addr), 'returned required fields');
}
```

## Common Issues

- **`Error: Class 'XML_RPC2_Client' not found`** — dependencies not installed. Run `composer install` first.
- **`PHP Fatal error: Cannot redeclare class Parallels`** — stale autoload cache. Run `composer dump-autoload` then retry.
- **Test marked Incomplete instead of Skipped for live tests** — `markTestIncomplete` blocks are for unwritten tests; for tests that need credentials use `markTestSkipped`. Replace the call accordingly.
- **Method name casing mismatch** — test method must match the existing stub name exactly (e.g. `testserverAddress` not `testServerAddress`). Check the existing stub before writing.
- **`Call to undefined method` on `$this->object`** — verify the method name in `src/Parallels.php` exactly; PHP method names are case-insensitive but the call must compile.

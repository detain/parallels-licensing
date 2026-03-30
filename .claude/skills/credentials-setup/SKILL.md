---
name: credentials-setup
description: Configures PARALLELS_KA_LOGIN, PARALLELS_KA_PASSWORD, and PARALLELS_KA_CLIENT for the Parallels KA API client. Use when user asks 'how do I authenticate', 'set credentials', 'connect to Parallels KA', 'use demo endpoint', or 'set up the Parallels library'. Covers constants vs constructor args and demo flag. Do NOT use for general key management operations (createKey, activateKey, etc.).
---
# credentials-setup

## Critical

- Never hardcode credentials in `src/Parallels.php` — pass via constructor args or PHP constants only.
- The `$demo` parameter accepts exactly `true` (demo URL), `false` (production URL), or a custom URL string. Any other value is passed as a literal URL.
- `$xmlOptions` defaults to `['sslverify' => false]`. Override only when you need SSL verification or other XML-RPC2 options.
- Constants must be defined **before** `new Parallels()` is called — the constructor reads them once at instantiation.

## Instructions

1. **Choose credential method: constants or constructor args.**
   - Constants approach — define before instantiating:
     ```php
     define('PARALLELS_KA_LOGIN', 'your-login');
     define('PARALLELS_KA_PASSWORD', 'your-password');
     define('PARALLELS_KA_CLIENT', 'your-client-id'); // optional
     $ka = new \Detain\Parallels\Parallels();
     ```
   - Constructor args approach — pass directly (takes precedence when non-null):
     ```php
     $ka = new \Detain\Parallels\Parallels('your-login', 'your-password', 'your-client-id');
     ```
   - Verify: `$ka->authInfo()` must return `['login' => 'your-login', 'password' => 'your-password']`.

2. **Select production vs demo endpoint** (4th constructor parameter `$demo`).
   - Production (default): `$demo = false` → `https://ka.parallels.com:7050/`
   - Demo/sandbox: `$demo = true` → `https://kademo.parallels.com:7050/`
   - Custom URL: `$demo = 'https://my-ka-host:7050/'`
   ```php
   // Demo endpoint with constants
   $ka = new \Detain\Parallels\Parallels(null, null, null, true);

   // Demo endpoint with explicit creds
   $ka = new \Detain\Parallels\Parallels('login', 'pass', null, true);
   ```
   - Verify: check `$ka->url` equals the expected endpoint string.

3. **Override XML-RPC2 options** (5th constructor parameter, optional).
   - Default is `['sslverify' => false]`. To enable SSL verification:
     ```php
     $ka = new \Detain\Parallels\Parallels(null, null, null, false, ['sslverify' => true]);
     ```
   - Verify: confirm `ext-soap` is installed (`php -m | grep soap`) and `pear/xml_rpc2` is present (`composer show pear/xml_rpc2`).

4. **Validate the connection** by calling `authInfo()` and confirming credentials round-trip correctly:
   ```php
   $auth = $ka->authInfo();
   assert($auth['login'] !== '' && $auth['login'] !== null);
   ```

## Examples

**User says:** "How do I connect to the Parallels KA API with my credentials?"

**Actions taken:**
1. Define constants (or pass constructor args).
2. Instantiate `Parallels` with the appropriate `$demo` flag.
3. Call `authInfo()` to verify.

**Result:**
```php
<?php
require_once 'vendor/autoload.php';

use Detain\Parallels\Parallels;

// Option A: constants
define('PARALLELS_KA_LOGIN', 'mylogin');
define('PARALLELS_KA_PASSWORD', 'mypassword');
$ka = new Parallels();               // production
$kaDemo = new Parallels(null, null, null, true); // demo

// Option B: constructor args (demo)
$ka = new Parallels('mylogin', 'mypassword', 'myclient', true);

var_dump($ka->authInfo());
// array(2) { ["login"]=> string(7) "mylogin" ["password"]=> string(10) "mypassword" }
```

## Common Issues

- **`Fatal error: Class 'XML_RPC2_Client' not found`** — `pear/xml_rpc2` is not installed. Run `composer install` and verify `vendor/pear/xml_rpc2/XML/RPC2/Client.php` exists.
- **`authInfo()` returns `['login' => null, 'password' => null]`** — constants were not defined before `new Parallels()` was called, and no constructor args were passed. Move `define()` calls above instantiation.
- **SSL handshake / certificate errors** — default `$xmlOptions` has `sslverify => false`. If you explicitly passed `['sslverify' => true]`, either revert to `false` or ensure the server certificate is valid and trusted.
- **Wrong endpoint used** — `$ka->url` shows production when demo was expected (or vice versa). Confirm the `$demo` argument is boolean `true`/`false`, not the string `'true'`/`'false'`.
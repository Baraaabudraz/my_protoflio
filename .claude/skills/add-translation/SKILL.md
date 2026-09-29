---
name: add-translation
description: Add or update UI strings in lang/ar.json and lang/en.json together, keeping both locales in sync. Use when adding user-facing text to Blade views/controllers, localizing a view, or when asked to fix missing translations.
---

This app uses JSON translations with English source strings as keys: `__('Projects')`. Default locale is `ar` (RTL), fallback `en`.

When adding or changing strings ($ARGUMENTS may list them, or collect them from the views you touched):

1. Wrap every user-facing literal in the Blade view with `{{ __('English text') }}` (or `__()` in PHP). Use `:placeholder` params for dynamic parts: `__('Welcome, :name', ['name' => $n])`.
2. Add the key to BOTH `lang/en.json` and `lang/ar.json`:
   - `en.json` value = the English key text.
   - `ar.json` value = a natural Arabic translation (Modern Standard Arabic, matching the tone of existing entries).
3. Keep valid JSON: 4-space indent, no trailing comma, UTF-8 unescaped Arabic. Don't reorder or rewrite existing entries; append new ones near related keys.
4. Don't duplicate keys already present — grep both files first.
5. Verify both files parse: `php -r "json_decode(file_get_contents('lang/ar.json'), flags: JSON_THROW_ON_ERROR); json_decode(file_get_contents('lang/en.json'), flags: JSON_THROW_ON_ERROR); echo 'ok';"`

Sync check (run when asked to audit): list keys present in `ar.json` but missing from `en.json` and vice versa:
`php -r '$a=json_decode(file_get_contents("lang/ar.json"),true);$e=json_decode(file_get_contents("lang/en.json"),true);echo "missing in en:\n".implode("\n",array_keys(array_diff_key($a,$e)))."\nmissing in ar:\n".implode("\n",array_keys(array_diff_key($e,$a)))."\n";'`

For RTL-sensitive layout in views, use the shared `$isRtl` / `$locale` variables rather than hard-coding direction.

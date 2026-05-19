# D2U Linkbox - Agent Notes

Rules only. Short. Actionable.

## Core Rules

- Namespace: `TobiasKrais\D2ULinkbox`
- Legacy namespace: `D2U_Linkbox`
- PHP classes: 4 spaces. Module files: tabs
- Comments only in English
- Backend labels always via `rex_i18n::msg()` with keys from `lang/`

## When Changing

- Keep backend translation keys in sync across all files under `lang/`
- For `d2u_machinery` links always use `\TobiasKrais\D2UHelper\FrontendHelper::isD2UMachineryExtensionActive()`, never old plugin checks
- In BS5 modules, solve colors through d2u_helper CSS variables. Do not add fixed inline background colors.
- For changes under `modules/24/*`: check or update changelog in `pages/help.changelog.php`
- Raise revision in `lib/Module.php` only once per release
- If target version in changelog already has `-DEV`: do not raise again in same phase
- Use real umlauts in changelog files, AGENTS.md, and README.md

## Maintenance

- Keep only recurring pitfalls, fixed conventions, and agent-relevant workflows here

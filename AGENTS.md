# D2U Linkbox - Redaxo Addon

A Redaxo 5 CMS addon for managing link boxes with images, titles, teasers, and various link types. Supports internal articles, documents, external URLs, and deep links to other D2U addons (d2u_immo, d2u_machinery, d2u_courses). Includes 6 different frontend display variants.

## Tech Stack

- **Language:** PHP >= 8.0
- **CMS:** Redaxo >= 5.19.0 (via d2u_helper)
- **Frontend Framework:** Bootstrap 4/5 (via d2u_helper templates)
- **Namespace:** `TobiasKrais\D2ULinkbox`

## Project Structure

```text
d2u_linkbox/
├── boot.php               # Addon bootstrap (extension points, permissions)
├── install.php             # Installation (database tables)
├── update.php              # Update (calls install.php)
├── uninstall.php           # Cleanup (database tables)
├── package.yml             # Addon configuration, version, dependencies
├── README.md
├── lang/                   # Backend translations (de_de, en_gb)
├── lib/                    # PHP classes
│   ├── Linkbox.php         # Linkbox model (multilingual, multiple link types)
│   ├── Category.php        # Category model
│   └── Module.php          # Module definitions and revisions
├── modules/                # 6 module variants in group 24
│   └── 24/
│       ├── 1/              # Linkboxen mit Überschrift in Bild
│       ├── 2/              # Linkboxen mit Überschrift unter Bild
│       ├── 3/              # Farbboxen mit seitlichem Bild
│       ├── 4/              # Slider
│       ├── 5/              # Linkboxen mit Text neben dem Bild
│       └── 6/              # Linkboxen mit Text und Hoverbild
└── pages/                  # Backend pages
    ├── index.php           # Page router
    ├── linkbox.php         # Linkbox management (CRUD)
    ├── category.php        # Category management
    ├── settings.php        # Addon settings (sort order)
    └── setup.php           # Module manager + changelog
```

## Coding Conventions

- **Namespace:** `TobiasKrais\D2ULinkbox` for all classes
- **Deprecated Namespace:** `D2U_Linkbox` (backward compatibility)
- **Naming:** camelCase for variables, PascalCase for classes
- **Indentation:** 4 spaces in PHP classes, tabs in module files
- **Comments:** English comments only
- **Backend labels:** Use `rex_i18n::msg()` with keys from `lang/` files

## Key Classes

| Class | Description |
| ----- | ----------- |
| `Linkbox` | Linkbox model: picture, pictogram, background color, multiple link types (article, document, external URL, d2u_immo, d2u_machinery, d2u_courses), categories, priority, online status. Multilingual. Implements `ITranslationHelper` |
| `Category` | Category model: name, associated linkboxes |
| `Module` | Module definitions and revision numbers for 6 modules |

## Database Tables

| Table | Description |
| ----- | ----------- |
| `rex_d2u_linkbox` | Linkboxes (language-independent): picture, pictogram, background color, link type, article/document/URL, category assignments, online status, priority |
| `rex_d2u_linkbox_lang` | Linkboxes (language-specific): title, teaser, language-specific picture/document/URL, translation status |
| `rex_d2u_linkbox_categories` | Categories: name |

## Architecture

### Extension Points

| Extension Point | Location | Purpose |
| --------------- | -------- | ------- |
| `ART_PRE_DELETED` | boot.php (backend) | Prevents deletion of articles used by linkboxes |
| `CLANG_DELETED` | boot.php (backend) | Cleans up language-specific data when a language is deleted |
| `D2U_HELPER_TRANSLATION_LIST` | boot.php (backend) | Registers addon in D2U Helper translation manager |
| `MEDIA_IS_IN_USE` | boot.php (backend) | Prevents deletion of media files used by linkboxes |

### Link Types

| Type | Target |
| ---- | ------ |
| `article` | Internal Redaxo article |
| `document` | Media pool document |
| `url` | External URL |
| `d2u_immo_property` | D2U Immo property |
| `d2u_machinery_industry_sector` | D2U Machinery industry sector |
| `d2u_machinery_machine` | D2U Machinery machine |
| `d2u_machinery_used_machine` | D2U Machinery used machine |
| `d2u_courses_category` | D2U Courses category |

### Modules

6 module variants in group 24:

| Module | Name | Description |
| ------ | ---- | ----------- |
| 24-1 | Linkboxen mit Überschrift in Bild | Image overlay title |
| 24-2 | Linkboxen mit Überschrift unter Bild | Title below image |
| 24-3 | Farbboxen mit seitlichem Bild | Colored boxes with side image |
| 24-4 | Slider | Sliding linkbox carousel |
| 24-5 | Linkboxen mit Text neben dem Bild | Text beside image |
| 24-6 | Linkboxen mit Text und Hoverbild | Text with hover image |

#### Module Versioning

Each module has a revision number defined in `lib/Module.php` inside the `getModules()` method. When a module is changed:

1. Add a changelog entry in `pages/setup.php` describing the change.
2. Increment the module's revision number in `Module::getModules()` by one.

**Important:** The revision only needs to be incremented **once per release**, not per commit. Check the changelog: if the version number is followed by `-DEV`, the release is still in development and no additional revision bump is needed.

## Settings

Managed via `pages/settings.php` and stored in `rex_config`:

- `default_sort` — Sort by name or priority (default: `name`)

## Dependencies

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `d2u_helper` | >= 1.14.0 | Backend/frontend helpers, module manager, translation interface |

### Optional Integrations

- `d2u_immo` — Deep linking to properties
- `d2u_machinery` (with plugins `industry_sectors`, `used_machines`) — Deep linking to machines
- `d2u_courses` — Deep linking to course categories

## Multi-language Support

- **Backend:** de_de, en_gb

## Versioning

This addon follows [Semantic Versioning](https://semver.org/). The version number is maintained in `package.yml`. During development, the changelog uses a `-DEV` suffix.

## Changelog

The changelog is located in `pages/setup.php`.

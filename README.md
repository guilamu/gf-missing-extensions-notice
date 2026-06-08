# Gravity Forms - Missing Extension Notice

[![Latest Release](https://img.shields.io/github/v/release/guilamu/gf-missing-extensions-notice?color=blue)](https://github.com/guilamu/gf-missing-extensions-notice/releases) [![License: AGPL-3.0](https://img.shields.io/badge/license-AGPL--3.0-green.svg)](LICENSE.txt) [![WordPress: 5.6+](https://img.shields.io/badge/WordPress-5.6%2B-blue.svg)](https://wordpress.org) [![PHP: 7.4+](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)

Alerts the administrator when a Gravity Forms form requires a missing extension (GF add-on, Gravity Perks, or any other).

![Plugin Screenshot](https://github.com/guilamu/gf-missing-extensions-notice/blob/main/screenshot.png)

## Main Feature Sections

### Form JSON Scan
- **Field Type Detection:** Detects custom field types such as `uid`, `form`, `chainedselect`, or `signature`.
- **Key Prefix Analysis:** Analyzes properties injected by extensions (e.g., `gppa-`, `gpnf-`, `gpls-`, etc.).
- **Meta Key Search:** Scans form-specific configurations (e.g., `gfpdf_form_settings`).

### Generic Field Type Scan
- **Dynamic Validation:** Compares saved field types in the form with registered field types in Gravity Forms.
- **Universal Support:** Flags unsupported field types even if they are not in the default markers list.

### Feed Scan
- **Feed Verification:** Queries `GFAPI::get_feeds()` for each active form.
- **Class Validation:** Checks if each `addon_slug` corresponds to an installed extension and that its PHP class is loaded.

## Key Features
- **JSON Detection:** Scans form structure for custom fields, property prefixes, and keys.
- **Multilingual:** Works with content in any language.
- **Translation-Ready:** All strings are internationalized.
- **Secure:** Restricts direct file access and sanitizes output correctly.
- **GitHub Updates:** Automatic updates from GitHub releases.

## Requirements
- Gravity Forms 2.8 or higher
- WordPress 5.6 or higher
- PHP 7.4 or higher

## Installation
1. Upload the `gf-missing-extensions-notice` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Verify that the warnings appear when editing a form with missing extensions

## FAQ

### Can I customize JSON markers?
Yes, use the `gf_missing_extension_markers` filter:
```php
add_filter( 'gf_missing_extension_markers', function( $markers ) {
    $markers['mon-prefixe-'] = array(
        'name'  => 'Mon extension',
        'type'  => 'key_prefix',
        'class' => 'Ma_Classe_Principale',
    );
    return $markers;
} );
```

### Can I add my own feed checkers?
Yes, use the `gf_missing_extension_feed_checkers` filter:
```php
add_filter( 'gf_missing_extension_feed_checkers', function( $checkers ) {
    $checkers['monplugin'] = array(
        'name'   => 'Mon Plugin',
        'class'  => 'MonPlugin_Loader',
        'check'  => 'is_plugin_active',
        'plugin' => 'monplugin/monplugin.php',
    );
    return $checkers;
} );
```

### How does generic detection work?
The plugin compares form fields to currently active fields in `GF_Fields::get_all()`. If a field type exists in the database but no plugin registers it, a warning is automatically shown.

## Project Structure
```
.
├── gf-missing-extensions-notice.php  # Main plugin file
├── README.md                         # This documentation file
├── LICENSE.txt                       # License agreement (GNU AGPL-3.0)
├── assets
│   ├── css
│   │   └── admin.css                 # Notice styling
│   └── js
│       └── admin.js                  # Notice generation and field checking
├── includes
│   ├── class-github-updater.php      # GitHub auto-updates and modal info
│   └── Parsedown.php                 # Markdown parser for details popup
└── languages
    ├── gf-miss-ext.pot               # Translation template file
    └── gf-miss-ext-fr_FR.po          # French translation file (source)
    └── gf-miss-ext-fr_FR.mo          # French translation file (binary)
```

## Changelog

### 1.0.1 - 2026-06-08
- Fix GitHub updater bug where the Gravity Forms version requirement was not displayed.
- Remove dead code functions `get_form_from_request()` and `get_requested_page()`.

### 1.0.0 - 2026-06-07
- Initial release.

## Security

If you discover a security vulnerability in this plugin, please report it responsibly through [GitHub Security Advisories](https://github.com/guilamu/gf-missing-extensions-notice/security/advisories/new). Do not open a public issue for security reports.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request on [GitHub](https://github.com/guilamu/gf-missing-extensions-notice).

For translations, the plugin uses WordPress i18n. You can contribute translations by editing the `.po` files in the `languages/` directory and generating the corresponding `.mo` files with the `wp i18n` CLI commands.

## License

This project is licensed under the GNU Affero General Public License v3.0 (AGPL-3.0) — see the [LICENSE](LICENSE.txt) file for details.

---

Made with love for the WordPress community

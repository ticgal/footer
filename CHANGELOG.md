# Changelog

## [3.0.0] - 2026-10-08
### Added
- GLPI 12 support (requires GLPI >= 12.0.0 and < 12.1.0)

### Changed
- Classes moved from `inc/` to `src/` with the `GlpiPlugin\Footer` namespace (`Config`, `Link`, `Menu`). The itemtypes stored in logs, display preferences, saved searches and dropdown translations are renamed on update
- The configuration form now asks for re-authentication, like the core configuration
- The footer and menu links are opened with `rel="noopener noreferrer"`
- The AJAX URL no longer depends on GLPI being installed at the web root
- The installation and uninstallation steps list the plugin classes explicitly instead of scanning `inc/`

### Security
- Stored XSS through the URL of a footer or menu link: the links are now built with DOM APIs instead of HTML concatenation, and the names are rendered as text
- Links whose URL is not http(s), mailto, tel or a local path (e.g. `javascript:`) are no longer rendered
- Menu links follow the `dropdown` right like footer links: central users without it could list and open them, including the ones meant for the other interface
- The AJAX endpoint answers with `application/json` and escapes the HTML-sensitive characters, instead of serving the stored names as `text/html`

### Fixed
- The "Fixed footer" option only accepts yes or no: other values were stored, or caused an SQL error
- Menu links only accept the known interfaces
- Uninstalling the plugin now also removes its dropdown translations, listing columns, history and saved searches, which were left behind and came back when the plugin was installed again

### Removed
- Unused icon dropdown code in the menu link class

## 2.0.2 25/08/2026
### Bugfixed
- Fa Icon fixed in menu link

## 2.0.1 30/07/2026
### Bugfixed
- Icon Dropdown fixed in menu link

## 2.0.0 27/01/2026
### Features
- GLPI 11 support

## 1.1.2 15/01/2025
### Bugfixed
- Fix marketplace page

## 1.1.1 09/05/2024
### Bugfixed
- Fix menu collapseed
- Fix new line and title
- Fix name with especial characters

## 1.1.0 30/01/2024
### Features
- New option to add links in the menu 
- New config footer fixed
- Do not add in modal windows
- Add ID to footer
### Bugfixed
- Fix anonymous user
- Fix css font family
- Fix recursive

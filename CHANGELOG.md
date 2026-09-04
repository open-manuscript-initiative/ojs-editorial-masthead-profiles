# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog, and releases use the version declared in `version.xml`.

## [Unreleased]

## [1.0.8.0] - 2026-09-04

### Added

- Moved the complete responsive editorial-card presentation into the plugin, including profile images, initial fallbacks, biographies, affiliations, websites, verified ORCID links, service dates, and public editor-profile links.
- Added a plugin-owned masthead stylesheet that is loaded only when the Editorial Masthead page is displayed.
- Expanded the linked public editor page to show the preferred public name and initials, Public Profile image, biography and homepage, public masthead affiliation, current roles, and verified ORCID without exposing private contact or account fields.
- Added a responsive, plugin-owned stylesheet for the public editor profile page.
- Added compatibility handling for OJS 3.5 masthead role collections that are keyed by sequential indexes instead of user-group IDs; the plugin restores the configured role order and rebuilds the native masthead data contract without modifying OJS core files.

### Changed

- Made template detection accept standard OJS resource prefixes and resolved application template paths.
- Updated installation guidance so legacy manual template overrides can be removed.

## [1.0.7.0] - 2026-08-30

### Changed

- Replaced journal-specific Hungarian template labels with locale keys.
- Replaced journal-specific colour styling with neutral, theme-compatible presentation.
- Added standalone integration with OJS's standard Editorial Masthead page: current masthead member names now link directly to their public profile pages without journal-specific theme edits.
- Preserved the upstream OJS 3.5 masthead markup, reviewer section, ORCID links, affiliations, editorial-history link, and standard CSS class names in the plugin-owned masthead template.
- Documented portability and third-party theme compatibility considerations.
- Documented OJS 3.5 Editorial Masthead compatibility caveats.
- Added portability checks against installation-specific identifiers and hard-coded template text.
- Added an AI assistance disclosure and human-review responsibility statement.
- Documented the current status of PKP's public discussion on AI contributions.

## [1.0.6.0] - 2026-08-30

### Documentation

- Expanded installation, compatibility, usage, localization, release, and Plugin Gallery documentation.
- Added contribution and security policies.
- Added automated repository checks for PHP syntax, plugin metadata, and required release files.

### Release

- Prepared the plugin for PKP Plugin Gallery submission.

## [1.0.5.0] - 2026-06-15

- Current plugin release declared by `version.xml`.

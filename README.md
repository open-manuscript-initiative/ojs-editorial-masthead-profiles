# Editorial Masthead Profiles Plugin for OJS

Adds public profile pages for members of an OJS editorial masthead. The plugin provides a dedicated `editorProfile` route and uses OJS user/profile data to present editorial team members in a journal-facing profile view.

## Requirements

- Open Journal Systems (OJS) 3.5.0-5
- PHP version supported by the corresponding OJS release

This release targets OJS 3.5.0-5. Later OJS 3.5 maintenance releases are expected to use the same template contract, but must be tested before their compatibility is declared. Before installing it on another OJS series, test compatibility in a non-production installation.

## Installation

### Manual installation

1. Download a release archive (`.tar.gz`) from the repository's Releases page.
2. In OJS, sign in as a Site Administrator.
3. Go to **Settings → Website → Plugins → Installed Plugins**.
4. Select **Upload A New Plugin** and upload the release archive.
5. Enable **Editorial Masthead Profiles** after installation.

For filesystem installation, extract the plugin into:

```text
plugins/generic/editorialMastheadProfiles
```

The directory name must be `editorialMastheadProfiles`.

## Usage

Once enabled, the plugin registers the `editorProfile` page handler. The route uses the current journal context plus the numeric user ID; exact URL appearance depends on the OJS URL and rewrite configuration.

The requested user must belong to the current journal context and must currently be opted in to an editorial-masthead user group. Requests for other users return 404.

The plugin replaces the standard OJS Editorial Masthead frontend template at display time with a plugin-owned responsive card view. It displays profile images, biographies, affiliations, websites, verified ORCID links, service dates, and links from current masthead member names to their public editor profiles. No journal-specific theme or OJS core-template modification is required.

Selecting a member's name opens a journal-scoped profile page. The page presents the member's preferred public name and avatar initials, profile image, localized biography and homepage from OJS's Public Profile form, together with the public masthead affiliation, current roles, and verified ORCID when those values are available. It deliberately does not expose email addresses, usernames, phone numbers, countries, postal addresses, API credentials, or other contact/account data.

OJS 3.5 requires custom page handlers to be supplied as objects through the `LoadHandler` hook. Version `1.0.8.1` uses this contract; earlier plugin versions that define `HANDLER_CLASS` can return HTTP 500 when a profile link is opened.

The plugin also detects the OJS 3.5 masthead role-key mismatch found in affected maintenance releases. When necessary, it restores the journal's configured role order and rebuilds the native `mastheadRoles`/`mastheadUsers` template contract through OJS repositories and relationship models, without patching the application or database schema.

Peer reviewers listed separately by OJS are not linked to editor profiles unless they are also current masthead members, because the public profile route deliberately validates current masthead membership.

### Theme compatibility

The standalone integration targets the standard OJS 3.5 Editorial Masthead template contract (`mastheadRoles`, `mastheadUsers`, `reviewers`, and related variables). Themes that only style the standard markup should continue to work because the plugin preserves the upstream page structure and CSS class names.

A theme that fully replaces the Editorial Masthead template with custom markup may require compatibility testing. While this plugin is enabled, its card template is selected at display time and its scoped stylesheet is loaded for that page only.

### Removing an old manual template override

Earlier installations may have copied a custom file over OJS's `templates/frontend/pages/editorialMasthead.tpl` or the corresponding `lib/pkp/templates` file. Version 1.0.8.0 no longer needs that change. Restore the template supplied by the installed OJS release, enable this plugin, and clear the OJS template cache. Future OJS updates can then replace application templates normally without removing the card presentation.

## Portability

The plugin does not contain a journal ID, journal path, hostname, database credentials, or installation-specific user IDs. It resolves the current OJS context at request time and scopes masthead membership to that context.

The profile presentation uses neutral styling and localized interface strings so it does not depend on the colours or language of a particular journal.

Database access uses Laravel's query builder rather than database-specific SQL. Nevertheless, release compatibility should be verified on supported OJS/database combinations before a broader compatibility claim is made.

### OJS 3.5 masthead caveat

The plugin depends on OJS's Editorial Masthead data and page being usable. Version 1.0.8.0 includes a plugin-level compatibility repair for the role-key defect present in OJS 3.5.0-5. Test the complete masthead-to-profile flow on the target database before production deployment.

## Localization

The plugin includes locale resources for English (`en`), German (`de`), and Hungarian (`hu`). Contributions for additional OJS locales are welcome.

## AI assistance disclosure

Generative AI assistance has been used in development and maintenance. See `AI_DISCLOSURE.md` for the disclosure and human-review responsibility statement.

PKP's public discussion in 2026 states that it did not yet have a settled general policy for AI contributions. This repository therefore discloses AI assistance proactively and keeps human maintainers responsible for every submitted change and release.

## Development and releases

The plugin follows the standard OJS generic-plugin layout and declares its metadata in `version.xml`.

When preparing a release:

1. Update the release number and date in `version.xml`.
2. Test the plugin against the supported OJS release(s).
3. Create a Git tag for the release.
4. Publish a `.tar.gz` release archive whose top-level directory is `editorialMastheadProfiles`.
5. Record user-visible changes in `CHANGELOG.md`.

## Plugin Gallery

The intended distribution channel is the PKP Plugin Gallery. A Gallery submission requires a published release package and a corresponding entry in PKP's `plugin-gallery` repository. Compatibility metadata in the Gallery entry should match the OJS versions actually tested by this project.

See PKP's Plugin Guide and Plugin Gallery documentation before submitting a release.

## Reporting issues

Please report reproducible bugs and compatibility problems through this repository's GitHub Issues. Include the OJS version, PHP version, database engine/version, plugin version, active theme, relevant error message or log excerpt, and steps to reproduce the problem.

## License

GNU General Public License v3.0 or later (GPL-3.0-or-later). See `LICENSE`.

## Maintainer

Open Manuscript Initiative

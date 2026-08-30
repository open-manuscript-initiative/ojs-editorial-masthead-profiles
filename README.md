# Editorial Masthead Profiles Plugin for OJS

Adds public profile pages for members of an OJS editorial masthead. The plugin provides a dedicated `editorProfile` route and uses OJS user/profile data to present editorial team members in a journal-facing profile view.

## Requirements

- Open Journal Systems (OJS) 3.5.x
- PHP version supported by the corresponding OJS release

This repository currently targets OJS 3.5. Before installing it on another OJS series, test compatibility in a non-production installation.

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

Once enabled, the plugin registers the `editorProfile` page handler. A profile URL has the form `/{journalPath}/{locale}/editorProfile/view/{userId}` (routing details may vary with URL rewriting).

The requested user must belong to the current journal context and must currently be opted in to an editorial-masthead user group. Requests for other users return 404.

### Integration note

The current release provides the public profile route but does **not** modify OJS core templates to turn names on the standard Editorial Masthead page into profile links. A theme or template integration must link a masthead member to the plugin route. This limitation is intentionally documented rather than assuming a site-specific theme customization.

## Portability

The plugin does not contain a journal ID, journal path, hostname, database credentials, or installation-specific user IDs. It resolves the current OJS context at request time and scopes masthead membership to that context.

The profile presentation uses neutral styling and localized interface strings so it does not depend on the colours or language of a particular journal.

Database access uses Laravel's query builder rather than database-specific SQL. Nevertheless, release compatibility should be verified on supported OJS/database combinations before a broader compatibility claim is made.

## Localization

The plugin includes locale resources for English (`en_US`), German (`de_DE`), and Hungarian (`hu`). Contributions for additional OJS locales are welcome.

## AI assistance disclosure

Generative AI assistance has been used in development and maintenance. See `AI_DISCLOSURE.md` for the disclosure and human-review responsibility statement.

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

Please report reproducible bugs and compatibility problems through this repository's GitHub Issues. Include the OJS version, PHP version, database engine/version, plugin version, relevant error message or log excerpt, and steps to reproduce the problem.

## License

GNU General Public License v3.0 or later (GPL-3.0-or-later). See `LICENSE`.

## Maintainer

Open Manuscript Initiative

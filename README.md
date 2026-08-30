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

Once enabled, the plugin registers the `editorProfile` page handler. Editorial masthead links can use this route to display a public profile for the selected editorial team member.

## Localization

The plugin includes locale resources for English (`en_US`), German (`de_DE`), and Hungarian (`hu`). Contributions for additional OJS locales are welcome.

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

Please report reproducible bugs and compatibility problems through this repository's GitHub Issues. Include the OJS version, PHP version, plugin version, relevant error message or log excerpt, and steps to reproduce the problem.

## License

GNU General Public License v3.0 or later (GPL-3.0-or-later). See `LICENSE`.

## Maintainer

Open Manuscript Initiative

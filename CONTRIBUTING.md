# Contributing

Contributions are welcome. Please use GitHub Issues for bug reports and feature proposals and pull requests for code or documentation changes.

## Bug reports

Include:

- OJS version
- PHP version
- database engine and version
- plugin version
- browser, when relevant
- steps to reproduce
- expected and actual behaviour
- relevant OJS/PHP log excerpt with secrets and personal data removed

## Pull requests

1. Create a focused branch from the current development branch.
2. Keep changes scoped to one issue or feature where practical.
3. Follow the coding conventions used by OJS/PKP and this plugin.
4. Preserve localization: user-facing strings must use locale keys rather than hard-coded interface text.
5. Do not introduce journal-specific IDs, hostnames, paths, colours, credentials, or assumptions into distributable code.
6. Test installation, enable/disable behaviour, and the affected public profile route on a supported OJS version.
7. Update documentation and `CHANGELOG.md` when behaviour changes.
8. Disclose material use of generative AI when it substantially generated or transformed submitted code, translations, documentation, tests, or other content.

## Compatibility

Do not claim compatibility with an OJS release that has not been tested. Changes that depend on PKP APIs should identify the OJS series and database engine used for verification.

## Licensing

By contributing, you agree that your contribution may be distributed under the project's GPL-3.0-or-later license.

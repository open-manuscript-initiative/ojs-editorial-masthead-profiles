# Editorial Masthead Profiles 1.0.7.0

This release generalizes the plugin for broader OJS 3.5 use and prepares it for PKP Plugin Gallery submission.

## Highlights

- Standalone integration with OJS 3.5's standard Editorial Masthead page.
- Current masthead member names link to public profile pages without journal-specific theme edits.
- Preserves upstream masthead roles, reviewer listing, ORCID links, affiliations, editorial history, and standard CSS class names.
- Removes installation-specific Hungarian UI text and journal-specific presentation colours.
- Adds English, German, and Hungarian localized profile interface strings.
- Adds portability CI guards against installation-specific identifiers and hard-coded template labels.
- Adds a transparent generative-AI assistance disclosure and contributor guidance.
- Improves release automation so a manual workflow run can create the version tag, build the PKP-installable archive, generate its MD5 checksum, and publish the GitHub Release assets.

## Compatibility

Target: OJS 3.5.x. A clean-install test on the current OJS 3.5 maintenance release and theme compatibility checks are required before submitting this release to the PKP Plugin Gallery.

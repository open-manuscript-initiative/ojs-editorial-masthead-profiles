# Release checklist

## v1.0.8.0

- [x] `version.xml` declares `1.0.8.0`.
- [x] AI assistance disclosure is present.
- [x] The complete responsive card view is owned by the plugin; no OJS or theme template is changed on disk.
- [x] Profile images, biographies, affiliations, websites, verified ORCID links, dates, and editor-profile links are represented.
- [x] Name links open responsive journal-scoped profiles and private account fields are not assigned to the frontend.
- [x] Affected OJS 3.5 sequential masthead role keys and configured role order are repaired through native repositories and relationship models.
- [x] The template hook, profile-image path, safe public links, scoped stylesheets, and unrelated-template behavior have regression checks.
- [x] English, German, and Hungarian interface strings are localized.
- [x] Repository CI checks PHP syntax, plugin metadata, required release files, and portability guards.
- [x] Release workflow builds `editorialMastheadProfiles-1.0.8.0.tar.gz` with the required top-level `editorialMastheadProfiles` directory.
- [x] Release workflow generates the matching `.md5` checksum file.
- [ ] Run the release workflow from `main` after this PR is merged.
- [ ] Verify that both custom release assets are attached to GitHub Release `v1.0.8.0`.
- [ ] Install the release archive on OJS 3.5.0-5 using the Default Theme.
- [ ] Verify another OJS 3.5-compatible theme.
- [ ] Verify that the official OJS masthead template is unmodified on the test installation.
- [ ] Submit the tested release to the PKP Plugin Gallery.

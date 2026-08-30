# Release checklist

## v1.0.7.0

- [x] `version.xml` declares `1.0.7.0`.
- [x] AI assistance disclosure is present.
- [x] Standard OJS 3.5 Editorial Masthead integration is implemented without journal-specific template edits.
- [x] English, German, and Hungarian interface strings are localized.
- [x] Repository CI checks PHP syntax, plugin metadata, required release files, and portability guards.
- [x] Release workflow builds `editorialMastheadProfiles-1.0.7.0.tar.gz` with the required top-level `editorialMastheadProfiles` directory.
- [x] Release workflow generates the matching `.md5` checksum file.
- [ ] Run the release workflow from `main` after this PR is merged.
- [ ] Verify that both custom release assets are attached to GitHub Release `v1.0.7.0`.
- [ ] Install the release archive on a clean/current OJS 3.5 installation using the Default Theme.
- [ ] Verify another OJS 3.5-compatible theme.
- [ ] Submit the tested release to the PKP Plugin Gallery.

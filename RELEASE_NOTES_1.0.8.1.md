# Editorial Masthead Profiles 1.0.8.1

This maintenance release fixes the public editor-profile route on OJS 3.5.

## Fixed

- Replaced the deprecated `HANDLER_CLASS` injection mechanism, which OJS 3.5 rejects with an exception and an HTTP 500 response.
- The plugin now supplies the `EditorProfileHandler` object through the fourth `LoadHandler` hook argument, as required by OJS 3.5.
- Added an automated regression test for route loading and a guard against reintroducing `HANDLER_CLASS`.

## Included from 1.0.8.0

- Self-contained responsive editorial-masthead cards.
- Clickable names and public editor profiles.
- OJS Public Profile image, biography, homepage, public masthead affiliation, current roles, and verified ORCID output.
- Compatibility repair for the OJS 3.5 masthead role-key mismatch.
- No OJS core or theme template modification.

## Upgrade note

Upgrade installations running `1.0.8.0` to `1.0.8.1`, then clear the OJS template and data caches before retesting a profile link.

## Compatibility

Primary target: OJS 3.5.0-5. The corrected route-injection contract is also present in OJS 3.5.0-4.

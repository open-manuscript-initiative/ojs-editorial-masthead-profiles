# Editorial Masthead Profiles 1.0.8.0

This release makes the complete card-based Editorial Masthead presentation self-contained in the plugin.

## Highlights

- Responsive cards for current editorial masthead members.
- Profile images with accessible initial fallbacks.
- Affiliations, biographies, websites, verified ORCID links, and service dates on the masthead page.
- Member names link to journal-scoped public editor profiles.
- Linked profile pages show the public name and initials, Public Profile image, biography and homepage, public masthead affiliation, current roles, and verified ORCID while excluding private contact and account data.
- Plugin-owned, page-scoped CSS with no journal-specific colours or identifiers.
- Compatibility repair for affected OJS 3.5 releases that expose masthead roles under sequential keys, including restoration of the configured role order.
- No OJS core or theme template change is required.
- Existing editorial history and previous-year reviewer output remains available.

## Upgrade note

If an earlier installation manually replaced the OJS Editorial Masthead template, restore the official template for that exact OJS release and clear the OJS cache. Version 1.0.8.0 supplies the card template and stylesheet itself.

## Compatibility

Primary target: OJS 3.5.0-5. Test the packaged release with the active journal theme before production rollout.

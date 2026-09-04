# Editorial Masthead Profiles 1.0.8.2

This maintenance release completes the OJS 3.5 public-profile route compatibility fixes.

## Fixed

- The public profile handler now renders `editorProfile.tpl` through the plugin's registered Smarty resource. OJS no longer misclassifies the path as an `app:` template below `lib/pkp/templates`.
- Missing or ineligible profiles now throw OJS's standard `NotFoundHttpException` instead of calling the unavailable `Dispatcher::handle404()` method, which caused HTTP 500.
- Automated checks now cover invalid-profile routing and reject future `handle404()` usage.

## Included fixes

- Version `1.0.8.1` replaced the deprecated `HANDLER_CLASS` mechanism with the OJS 3.5 `LoadHandler` handler-object contract.
- Version `1.0.8.0` made the card layout and linked public profiles self-contained in the plugin.

## Upgrade note

Upgrade installations running `1.0.8.0` or `1.0.8.1` to `1.0.8.2`. Clear the OJS template and data caches, then retest a current masthead member and an invalid profile URL.

## Compatibility

Primary target: OJS 3.5.0-5. The corrected routing and membership APIs are also available in OJS 3.5.0-4.

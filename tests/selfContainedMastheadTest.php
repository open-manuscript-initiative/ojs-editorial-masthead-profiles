<?php

namespace APP\facades {
    class Repo
    {
    }
}

namespace APP\file {
    class PublicFileManager
    {
        public function getSiteFilesPath(): string
        {
            return 'public/site';
        }
    }
}

namespace APP\template {
    class TemplateManager
    {
    }
}

namespace PKP\core {
    class PKPApplication
    {
        public const ROUTE_PAGE = 1;
    }
}

namespace PKP\config {
    class Config
    {
    }
}

namespace PKP\plugins {
    class GenericPlugin
    {
        public function register($category, $path, $mainContextId = null): bool
        {
            return true;
        }

        public function getEnabled($contextId = null): bool
        {
            return true;
        }

        public function getPluginPath(): string
        {
            return 'plugins/generic/editorialMastheadProfiles';
        }

        public function getTemplateResource($template = null, $inCore = false): string
        {
            return 'plugin-resource:' . $template;
        }

        public function getRequest()
        {
            return null;
        }
    }

    class Hook
    {
        public static function add($name, $callback): void
        {
        }
    }
}

namespace PKP\template {
    class PKPTemplateManager
    {
        public const STYLE_SEQUENCE_LATE = 15;
    }
}

namespace PKP\handler {
    class PKPHandler
    {
        public function __construct()
        {
        }
    }
}

namespace PKP\userGroup\relationships {
    class UserUserGroup
    {
    }
}

namespace Symfony\Component\HttpKernel\Exception {
    class NotFoundHttpException extends \Exception
    {
    }
}

namespace {
    use APP\plugins\generic\editorialMastheadProfiles\EditorialMastheadProfilesPlugin;

    require dirname(__DIR__) . '/EditorialMastheadProfilesPlugin.php';

    final class FakeRole
    {
        public function __construct(private int $id)
        {
        }

        public function getId(): int
        {
            return $this->id;
        }
    }

    final class FakeEloquentRole
    {
        public function __construct(private int $id)
        {
        }

        public function getKey(): int
        {
            return $this->id;
        }
    }

    final class FakeUser
    {
        public function __construct(private int $id, private array $profileImage)
        {
        }

        public function getId(): int
        {
            return $this->id;
        }

        public function getData(string $name)
        {
            return $name === 'profileImage' ? $this->profileImage : null;
        }
    }

    final class FakeTemplateManager
    {
        public array $styles = [];

        public function __construct(public array $vars)
        {
        }

        public function getTemplateVars(string $name)
        {
            return $this->vars[$name] ?? null;
        }

        public function assign($name, $value = null): void
        {
            if (is_array($name)) {
                $this->vars = array_merge($this->vars, $name);
                return;
            }
            $this->vars[$name] = $value;
        }

        public function addStyleSheet(string $name, string $url, array $options = []): void
        {
            $this->styles[$name] = ['url' => $url, 'options' => $options];
        }
    }

    final class FakeInvalidProfileRequest
    {
        public function getContext(): object
        {
            return new \stdClass();
        }
    }

    function expectSame($expected, $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException(
                $message . '\nExpected: ' . var_export($expected, true) . '\nActual: ' . var_export($actual, true)
            );
        }
    }

    $plugin = new EditorialMastheadProfilesPlugin();

    $sourceFile = 'pages/editorProfile/index.php';
    $profileHandler = null;
    $loadHandlerArgs = ['editorProfile', 'view', &$sourceFile, &$profileHandler];
    expectSame(
        true,
        $plugin->callbackLoadHandler('LoadHandler', $loadHandlerArgs),
        'The plugin must handle the editorProfile route.'
    );
    expectSame(
        true,
        $profileHandler instanceof \APP\plugins\generic\editorialMastheadProfiles\pages\EditorProfileHandler,
        'OJS 3.5 must receive the profile handler object through LoadHandler argument 4.'
    );
    $profileTemplateProperty = new ReflectionProperty($profileHandler, 'profileTemplateResource');
    $profileTemplateProperty->setAccessible(true);
    expectSame(
        'plugin-resource:editorProfile.tpl',
        $profileTemplateProperty->getValue($profileHandler),
        'The profile handler must receive the plugin-registered Smarty template resource.'
    );
    expectSame(false, defined('HANDLER_CLASS'), 'OJS 3.5 rejects the deprecated HANDLER_CLASS injection mechanism.');
    try {
        $profileHandler->view([0], new FakeInvalidProfileRequest());
        throw new RuntimeException('An invalid public profile ID must stop with a 404 exception.');
    } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
        expectSame(true, true, 'Invalid public profile IDs use the OJS-compatible 404 exception.');
    }

    $user = new FakeUser(12, ['uploadName' => 'profileImage-12.jpg']);
    $templateManager = new FakeTemplateManager([
        'baseUrl' => 'https://example.test/ojs',
        'mastheadRoles' => [7 => new FakeRole(7)],
        'mastheadUsers' => [7 => [12 => ['user' => $user, 'dateStart' => '2026']]],
    ]);
    $template = 'app:frontend/pages/editorialMasthead.tpl';
    $args = [$templateManager, &$template];

    expectSame(false, $plugin->callbackTemplateDisplay('TemplateManager::display', $args), 'The hook must not stop OJS rendering.');
    expectSame('plugin-resource:editorialMasthead.tpl', $template, 'The standard masthead must use the plugin template.');
    expectSame(
        'https://example.test/ojs/public/site/profileImage-12.jpg',
        $templateManager->vars['editorialMastheadProfileImageUrls'][12] ?? null,
        'Profile images must use the configured public site files path.'
    );
    expectSame(
        'https://example.test/ojs/plugins/generic/editorialMastheadProfiles/styles/editorialMastheadCards.css',
        $templateManager->styles['editorialMastheadProfilesCards']['url'] ?? null,
        'The card stylesheet must be loaded from the plugin.'
    );

    $unrelatedTemplate = 'frontend/pages/indexJournal.tpl';
    $unrelatedArgs = [$templateManager, &$unrelatedTemplate];
    $plugin->callbackTemplateDisplay('TemplateManager::display', $unrelatedArgs);
    expectSame('frontend/pages/indexJournal.tpl', $unrelatedTemplate, 'Unrelated OJS templates must remain unchanged.');

    $absoluteTemplate = '/var/www/ojs/lib/pkp/templates/frontend/pages/editorialMasthead.tpl';
    $absoluteArgs = [$templateManager, &$absoluteTemplate];
    $plugin->callbackTemplateDisplay('TemplateManager::display', $absoluteArgs);
    expectSame('plugin-resource:editorialMasthead.tpl', $absoluteTemplate, 'Resolved OJS masthead paths must also be recognized.');

    $eloquentTemplateManager = new FakeTemplateManager([
        'baseUrl' => 'https://example.test/ojs',
        'mastheadRoles' => [0 => new FakeEloquentRole(9)],
        'mastheadUsers' => [9 => []],
    ]);
    $eloquentTemplate = 'frontend/pages/editorialMasthead.tpl';
    $eloquentArgs = [$eloquentTemplateManager, &$eloquentTemplate];
    $plugin->callbackTemplateDisplay('TemplateManager::display', $eloquentArgs);
    expectSame(
        [9],
        array_keys($eloquentTemplateManager->vars['mastheadRoles']),
        'OJS 3.5 Eloquent masthead roles must be keyed by their model primary keys.'
    );

    echo "Self-contained masthead checks passed\n";
}

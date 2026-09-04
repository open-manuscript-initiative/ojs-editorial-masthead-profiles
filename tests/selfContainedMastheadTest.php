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

namespace PKP\userGroup\relationships {
    class UserUserGroup
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

    function expectSame($expected, $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException(
                $message . '\nExpected: ' . var_export($expected, true) . '\nActual: ' . var_export($actual, true)
            );
        }
    }

    $plugin = new EditorialMastheadProfilesPlugin();
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

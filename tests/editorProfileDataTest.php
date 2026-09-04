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

namespace PKP\facades {
    class Locale
    {
    }
}

namespace PKP\handler {
    class PKPHandler
    {
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
    use APP\plugins\generic\editorialMastheadProfiles\pages\EditorProfileHandler;

    require dirname(__DIR__) . '/pages/EditorProfileHandler.php';

    final class FakeProfileUser
    {
        public function __construct(private $profileImage)
        {
        }

        public function getData(string $name)
        {
            return $name === 'profileImage' ? $this->profileImage : null;
        }
    }

    final class FakeProfileRequest
    {
        public function getBaseUrl(): string
        {
            return 'https://example.test/ojs';
        }
    }

    function expectProfileValue($expected, $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException(
                $message . '\nExpected: ' . var_export($expected, true) . '\nActual: ' . var_export($actual, true)
            );
        }
    }

    function invokePrivate(object $object, string $method, array $arguments = [])
    {
        $reflection = new ReflectionMethod($object, $method);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($object, $arguments);
    }

    $handler = new EditorProfileHandler();

    expectProfileValue(
        'https://orcid.org/0000-0002-1825-0097',
        invokePrivate($handler, 'normalizeOrcid', ['https://orcid.org/0000-0002-1825-0097']),
        'A valid ORCID URL must be normalized.'
    );
    expectProfileValue(
        '',
        invokePrivate($handler, 'normalizeOrcid', ['javascript:alert(1)']),
        'An invalid ORCID value must not become a public link.'
    );
    expectProfileValue(
        'https://example.org/profile',
        invokePrivate($handler, 'normalizeHttpUrl', ['example.org/profile']),
        'A homepage without a scheme must receive HTTPS.'
    );
    expectProfileValue(
        '',
        invokePrivate($handler, 'normalizeHttpUrl', ['javascript://example.org/%0Aalert(1)']),
        'Only HTTP(S) homepage links may be displayed.'
    );
    expectProfileValue(
        'https://example.test/ojs/public/site/profileImage-7.jpg',
        invokePrivate(
            $handler,
            'getProfileImageUrl',
            [new FakeProfileUser(['uploadName' => '../profileImage-7.jpg']), new FakeProfileRequest()]
        ),
        'Profile-image URLs must use the OJS public site path and a safe filename.'
    );

    echo "Editor profile data checks passed\n";
}

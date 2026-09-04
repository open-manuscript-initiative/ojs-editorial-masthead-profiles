<?php
namespace APP\plugins\generic\editorialMastheadProfiles\pages;

use APP\facades\Repo;
use APP\file\PublicFileManager;
use APP\template\TemplateManager;
use PKP\core\PKPApplication;
use PKP\handler\PKPHandler;
use PKP\template\PKPTemplateManager;
use PKP\userGroup\relationships\UserUserGroup;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditorProfileHandler extends PKPHandler
{
    public function __construct(private string $profileTemplateResource)
    {
        parent::__construct();
    }

    public function index($args, $request)
    {
        return $this->view($args, $request);
    }

    public function view($args, $request)
    {
        $context = $request->getContext();
        if (!$context) {
            throw new NotFoundHttpException();
        }

        $userId = isset($args[0]) ? (int) $args[0] : 0;
        if ($userId <= 0) {
            throw new NotFoundHttpException();
        }

        $user = Repo::user()->get($userId);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        if (!$this->isCurrentMastheadUser($userId, (int) $context->getId())) {
            throw new NotFoundHttpException();
        }

        $roles = $this->getMastheadRoleNames($userId, (int) $context->getId(), $context);
        $templateMgr = TemplateManager::getManager($request);
        $profileOrcid = method_exists($user, 'hasVerifiedOrcid') && $user->hasVerifiedOrcid()
            ? $this->normalizeOrcid((string) $user->getData('orcid'))
            : '';
        $templateMgr->assign([
            'profileName' => $user->getFullName(),
            'profileInitials' => method_exists($user, 'getDisplayInitials')
                ? $user->getDisplayInitials()
                : mb_substr($user->getFullName(), 0, 1),
            'profileRoles' => $roles,
            'profileImageUrl' => $this->getProfileImageUrl($user, $request),
            'profileAffiliation' => (string) $user->getLocalizedData('affiliation'),
            'profileBiography' => (string) $user->getLocalizedData('biography'),
            'profileOrcid' => $profileOrcid,
            'profileUrl' => $this->normalizeHttpUrl((string) $user->getData('url')),
            'editorialMastheadUrl' => $request->getDispatcher()->url($request, PKPApplication::ROUTE_PAGE, $context->getPath(), 'about', 'editorialMasthead'),
        ]);
        $templateMgr->addStyleSheet(
            'editorialMastheadProfilesProfile',
            rtrim($request->getBaseUrl(), '/')
                . '/'
                . trim(EDITORIAL_MASTHEAD_PROFILES_PLUGIN_PATH, '/')
                . '/styles/editorProfile.css',
            ['priority' => PKPTemplateManager::STYLE_SEQUENCE_LATE]
        );

        return $templateMgr->display($this->profileTemplateResource);
    }

    private function isCurrentMastheadUser(int $userId, int $contextId): bool
    {
        return UserUserGroup::withUserId($userId)
            ->withContextId($contextId)
            ->withActive()
            ->withMasthead()
            ->exists();
    }

    private function getMastheadRoleNames(int $userId, int $contextId, $context): array
    {
        $roleIds = UserUserGroup::withUserId($userId)
            ->withContextId($contextId)
            ->withActive()
            ->withMasthead()
            ->pluck('user_group_id')
            ->map(fn ($roleId) => (int) $roleId)
            ->unique()
            ->values()
            ->all();

        $savedOrder = array_map('intval', (array) $context->getData('mastheadUserGroupIds'));
        $savedPositions = array_flip($savedOrder);
        usort($roleIds, function (int $firstRoleId, int $secondRoleId) use ($savedPositions): int {
            $positionComparison = ($savedPositions[$firstRoleId] ?? PHP_INT_MAX)
                <=> ($savedPositions[$secondRoleId] ?? PHP_INT_MAX);

            return $positionComparison !== 0
                ? $positionComparison
                : $firstRoleId <=> $secondRoleId;
        });

        $roleNames = [];
        foreach ($roleIds as $roleId) {
            $role = Repo::userGroup()->get($roleId, $contextId);
            $roleName = $role ? (string) $role->getLocalizedData('name') : '';
            if ($roleName !== '' && !in_array($roleName, $roleNames, true)) {
                $roleNames[] = $roleName;
            }
        }

        return $roleNames;
    }

    private function getProfileImageUrl($user, $request): string
    {
        $profileImage = $user->getData('profileImage');
        if (is_string($profileImage) && $profileImage !== '') {
            $decoded = json_decode($profileImage, true);
            $profileImage = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($profileImage) || empty($profileImage['uploadName'])) {
            return '';
        }

        $publicFileManager = new PublicFileManager();

        return rtrim($request->getBaseUrl(), '/')
            . '/'
            . trim(str_replace('\\', '/', $publicFileManager->getSiteFilesPath()), '/')
            . '/'
            . rawurlencode(basename((string) $profileImage['uploadName']));
    }

    private function normalizeOrcid(string $orcid): string
    {
        $orcid = trim($orcid);
        $orcid = preg_replace('#^https?://(?:www\.)?orcid\.org/#i', '', $orcid);

        return preg_match('/^0000-\d{4}-\d{4}-\d{3}[0-9X]$/i', (string) $orcid)
            ? 'https://orcid.org/' . strtoupper((string) $orcid)
            : '';
    }

    private function normalizeHttpUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (
            preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)
            && !preg_match('#^https?://#i', $url)
        ) {
            return '';
        }
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        $parts = parse_url($url);

        return is_array($parts)
            && in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
            && !empty($parts['host'])
                ? $url
                : '';
    }

}

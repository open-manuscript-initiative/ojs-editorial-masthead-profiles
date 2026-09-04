<?php
namespace APP\plugins\generic\editorialMastheadProfiles;

use APP\facades\Repo;
use APP\file\PublicFileManager;
use DateTime;
use PKP\config\Config;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\template\PKPTemplateManager;
use PKP\userGroup\relationships\UserUserGroup;

class EditorialMastheadProfilesPlugin extends GenericPlugin
{
    private const EDITORIAL_MASTHEAD_TEMPLATE = 'frontend/pages/editorialMasthead.tpl';

    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if (
            $success
            && Config::getVar('general', 'installed')
            && $this->getEnabled($mainContextId)
        ) {
            Hook::add('LoadHandler', [$this, 'callbackLoadHandler']);
            Hook::add('TemplateManager::display', [$this, 'callbackTemplateDisplay']);
        }
        return $success;
    }

    public function getDisplayName(): string
    {
        return __('plugins.generic.editorialMastheadProfiles.displayName');
    }

    public function getDescription(): string
    {
        return __('plugins.generic.editorialMastheadProfiles.description');
    }

    public function callbackLoadHandler($hookName, $args): bool
    {
        $page = $args[0] ?? null;
        if ($page !== 'editorProfile') {
            return false;
        }

        if (!defined('EDITORIAL_MASTHEAD_PROFILES_PLUGIN_PATH')) {
            define('EDITORIAL_MASTHEAD_PROFILES_PLUGIN_PATH', $this->getPluginPath());
        }

        define('HANDLER_CLASS', 'APP\\plugins\\generic\\editorialMastheadProfiles\\pages\\EditorProfileHandler');
        $sourceFile =& $args[2];
        $sourceFile = $this->getPluginPath() . '/pages/EditorProfileHandler.php';
        return true;
    }

    /**
     * Replace OJS's standard Editorial Masthead template with the plugin-owned
     * card view. No application or theme template is changed on disk.
     */
    public function callbackTemplateDisplay($hookName, $args): bool
    {
        if (!array_key_exists(1, $args) || !is_string($args[1])) {
            return false;
        }

        $template =& $args[1];
        if (!$this->isEditorialMastheadTemplate($template)) {
            return false;
        }

        $templateManager = $args[0] ?? null;
        if ($templateManager) {
            $this->prepareMastheadData($templateManager);
            $this->assignProfileImageUrls($templateManager);
            $this->addMastheadStylesheet($templateManager);
        }

        $template = $this->getTemplateResource('editorialMasthead.tpl');
        return false;
    }

    private function isEditorialMastheadTemplate(string $template): bool
    {
        $normalizedTemplate = preg_replace('/^(?:tpl|app|core):/', '', $template);
        $normalizedTemplate = str_replace('\\', '/', ltrim((string) $normalizedTemplate, '/'));

        return $normalizedTemplate === self::EDITORIAL_MASTHEAD_TEMPLATE
            || str_ends_with($normalizedTemplate, '/templates/' . self::EDITORIAL_MASTHEAD_TEMPLATE);
    }

    /**
     * Normalize masthead role keys and rebuild the user list when an affected
     * OJS 3.5 release provides roles under sequential array keys.
     */
    private function prepareMastheadData($templateManager): void
    {
        $mastheadRoles = $templateManager->getTemplateVars('mastheadRoles');
        if (!is_iterable($mastheadRoles)) {
            return;
        }

        $rolesById = [];
        $roleKeysNeedRepair = false;
        foreach ($mastheadRoles as $key => $mastheadRole) {
            if (!is_object($mastheadRole)) {
                continue;
            }

            $roleId = $this->getMastheadRoleId($mastheadRole);
            if ($roleId <= 0) {
                continue;
            }
            $rolesById[$roleId] = $mastheadRole;
            if ((string) $key !== (string) $roleId) {
                $roleKeysNeedRepair = true;
            }
        }

        if (!$roleKeysNeedRepair || !$rolesById) {
            return;
        }

        $request = $this->getRequest();
        $context = $request ? $request->getContext() : null;
        if (!$context) {
            $templateManager->assign('mastheadRoles', $rolesById);
            return;
        }

        $mastheadRoleOrder = array_map('intval', (array) $context->getData('mastheadUserGroupIds'));
        $mastheadRolePositions = array_flip($mastheadRoleOrder);
        $orderedRoles = array_values($rolesById);
        usort($orderedRoles, function ($firstRole, $secondRole) use ($mastheadRolePositions): int {
            $firstRoleId = $this->getMastheadRoleId($firstRole);
            $secondRoleId = $this->getMastheadRoleId($secondRole);
            $firstPosition = $mastheadRolePositions[$firstRoleId] ?? PHP_INT_MAX;
            $secondPosition = $mastheadRolePositions[$secondRoleId] ?? PHP_INT_MAX;
            $positionComparison = $firstPosition <=> $secondPosition;

            return $positionComparison !== 0
                ? $positionComparison
                : $firstRoleId <=> $secondRoleId;
        });
        $rolesById = [];
        foreach ($orderedRoles as $mastheadRole) {
            $rolesById[$this->getMastheadRoleId($mastheadRole)] = $mastheadRole;
        }
        $templateManager->assign('mastheadRoles', $rolesById);

        try {
            $templateManager->assign(
                'mastheadUsers',
                $this->getCurrentMastheadUsers(array_values($rolesById), (int) $context->getId())
            );
        } catch (\Throwable $exception) {
            error_log(
                'Editorial Masthead Profiles could not rebuild masthead users: '
                . $exception->getMessage()
            );
        }
    }

    /**
     * Reproduce the native OJS 3.5 masthead data contract using role IDs as keys.
     */
    private function getCurrentMastheadUsers(array $mastheadRoles, int $contextId): array
    {
        $userIdsByRoleId = Repo::userGroup()->getMastheadUserIdsByRoleIds($mastheadRoles, $contextId);
        $mastheadUsers = [];

        foreach ($mastheadRoles as $mastheadRole) {
            $roleId = $this->getMastheadRoleId($mastheadRole);
            foreach ($userIdsByRoleId[$roleId] ?? [] as $userId) {
                $user = Repo::user()->get((int) $userId);
                if (!$user) {
                    continue;
                }

                $userUserGroup = UserUserGroup::withUserId($user->getId())
                    ->withUserGroupIds([$roleId])
                    ->withActive()
                    ->withMasthead()
                    ->first();
                if (!$userUserGroup) {
                    continue;
                }

                $startDate = $userUserGroup->dateStart
                    ? (new DateTime($userUserGroup->dateStart))->format('Y')
                    : '';
                $mastheadUsers[$roleId][$user->getId()] = [
                    'user' => $user,
                    'dateStart' => $startDate,
                ];
            }
        }

        return $mastheadUsers;
    }

    /**
     * OJS 3.5 masthead roles are Eloquent models. Use their primary key while
     * retaining compatibility with older data objects that expose getId().
     */
    private function getMastheadRoleId(object $mastheadRole): int
    {
        if (method_exists($mastheadRole, 'getKey')) {
            return (int) $mastheadRole->getKey();
        }
        if (method_exists($mastheadRole, 'getId')) {
            return (int) $mastheadRole->getId();
        }

        return (int) ($mastheadRole->id ?? $mastheadRole->userGroupId ?? 0);
    }

    private function assignProfileImageUrls($templateManager): void
    {
        $mastheadUsers = $templateManager->getTemplateVars('mastheadUsers');
        if (!is_iterable($mastheadUsers)) {
            $templateManager->assign('editorialMastheadProfileImageUrls', []);
            return;
        }

        $baseUrl = rtrim((string) $templateManager->getTemplateVars('baseUrl'), '/');
        $publicFileManager = new PublicFileManager();
        $profileImageBaseUrl = $baseUrl . '/' . trim($publicFileManager->getSiteFilesPath(), '/');
        $profileImageUrls = [];

        foreach ($mastheadUsers as $roleUsers) {
            if (!is_iterable($roleUsers)) {
                continue;
            }
            foreach ($roleUsers as $mastheadUser) {
                $user = is_array($mastheadUser) ? ($mastheadUser['user'] ?? null) : null;
                if (!$user || !method_exists($user, 'getData') || !method_exists($user, 'getId')) {
                    continue;
                }

                $profileImage = $user->getData('profileImage');
                if (is_string($profileImage) && $profileImage !== '') {
                    $decodedProfileImage = json_decode($profileImage, true);
                    $profileImage = is_array($decodedProfileImage) ? $decodedProfileImage : [];
                }

                $uploadName = is_array($profileImage) ? ($profileImage['uploadName'] ?? '') : '';
                if (!is_string($uploadName) || $uploadName === '') {
                    continue;
                }

                $profileImageUrls[(int) $user->getId()] = $profileImageBaseUrl
                    . '/'
                    . rawurlencode(basename($uploadName));
            }
        }

        $templateManager->assign('editorialMastheadProfileImageUrls', $profileImageUrls);
    }

    private function addMastheadStylesheet($templateManager): void
    {
        $baseUrl = rtrim((string) $templateManager->getTemplateVars('baseUrl'), '/');
        if ($baseUrl === '') {
            return;
        }

        $templateManager->addStyleSheet(
            'editorialMastheadProfilesCards',
            $baseUrl . '/' . trim($this->getPluginPath(), '/') . '/styles/editorialMastheadCards.css',
            ['priority' => PKPTemplateManager::STYLE_SEQUENCE_LATE]
        );
    }
}

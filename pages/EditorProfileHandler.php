<?php
namespace APP\plugins\generic\editorialMastheadProfiles\pages;

use APP\template\TemplateManager;
use Illuminate\Support\Facades\DB;
use PKP\config\Config;
use PKP\core\PKPApplication;
use PKP\db\DAORegistry;
use PKP\handler\PKPHandler;

class EditorProfileHandler extends PKPHandler
{
    public function index($args, $request)
    {
        return $this->view($args, $request);
    }

    public function view($args, $request)
    {
        $context = $request->getContext();
        if (!$context) {
            return $request->getDispatcher()->handle404();
        }

        $userId = isset($args[0]) ? (int) $args[0] : 0;
        if ($userId <= 0) {
            return $request->getDispatcher()->handle404();
        }

        $userDao = DAORegistry::getDAO('UserDAO');
        $user = $userDao ? $userDao->getById($userId) : null;
        if (!$user) {
            return $request->getDispatcher()->handle404();
        }

        if (!$this->isCurrentMastheadUser($userId, (int) $context->getId())) {
            return $request->getDispatcher()->handle404();
        }

        $roles = $this->getMastheadRoleNames($userId, (int) $context->getId(), $context);
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign([
            'profileUser' => $user,
            'profileName' => $user->getFullName(),
            'profileRoles' => $roles,
            'profileImageUrl' => $this->getProfileImageUrl($user, $request),
            'profileAffiliation' => (string) $user->getLocalizedData('affiliation'),
            'profileBiography' => (string) $user->getLocalizedData('biography'),
            'profileOrcid' => $this->normalizeOrcid((string) $user->getData('orcid')),
            'profileUrl' => $this->normalizeUrl((string) $user->getData('url')),
            'profileCountry' => (string) $user->getData('country'),
            'editorialMastheadUrl' => $request->getDispatcher()->url($request, PKPApplication::ROUTE_PAGE, $context->getPath(), 'about', 'editorialMasthead'),
        ]);

        return $templateMgr->display(EDITORIAL_MASTHEAD_PROFILES_PLUGIN_PATH . '/templates/editorProfile.tpl');
    }

    private function isCurrentMastheadUser(int $userId, int $contextId): bool
    {
        return DB::table('user_user_groups as uug')
            ->join('user_groups as ug', 'ug.user_group_id', '=', 'uug.user_group_id')
            ->where('uug.user_id', $userId)
            ->where('ug.context_id', $contextId)
            ->where('uug.masthead', 1)
            ->where(function ($q) {
                $q->whereNull('uug.date_end')->orWhere('uug.date_end', '>=', date('Y-m-d 00:00:00'));
            })
            ->exists();
    }

    private function getMastheadRoleNames(int $userId, int $contextId, $context): array
    {
        $locale = method_exists($context, 'getPrimaryLocale') ? $context->getPrimaryLocale() : null;
        return DB::table('user_user_groups as uug')
            ->join('user_groups as ug', 'ug.user_group_id', '=', 'uug.user_group_id')
            ->join('user_group_settings as ugs', function ($join) use ($locale) {
                $join->on('ugs.user_group_id', '=', 'ug.user_group_id')
                    ->where('ugs.setting_name', '=', 'name');
                if ($locale) {
                    $join->where(function ($q) use ($locale) {
                        $q->where('ugs.locale', '=', $locale)
                          ->orWhereNull('ugs.locale')
                          ->orWhere('ugs.locale', '=', '');
                    });
                }
            })
            ->where('uug.user_id', $userId)
            ->where('ug.context_id', $contextId)
            ->where('uug.masthead', 1)
            ->where(function ($q) {
                $q->whereNull('uug.date_end')->orWhere('uug.date_end', '>=', date('Y-m-d 00:00:00'));
            })
            ->orderBy('uug.user_group_id')
            ->pluck('ugs.setting_value')
            ->filter()
            ->unique()
            ->values()
            ->all();
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
        $publicFilesDir = Config::getVar('files', 'public_files_dir') ?: 'public';
        return rtrim($request->getBaseUrl(), '/') . '/' . trim($publicFilesDir, '/') . '/site/' . rawurlencode($profileImage['uploadName']);
    }

    private function normalizeOrcid(string $orcid): string
    {
        $orcid = trim($orcid);
        if ($orcid === '') return '';
        if (preg_match('/^https?:\/\//i', $orcid)) return $orcid;
        if (preg_match('/^0000-\d{4}-\d{4}-\d{3}[0-9X]$/i', $orcid)) return 'https://orcid.org/' . $orcid;
        return $orcid;
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') return '';
        if (preg_match('/^https?:\/\//i', $url)) return $url;
        return 'https://' . $url;
    }
}

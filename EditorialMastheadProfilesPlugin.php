<?php
namespace APP\plugins\generic\editorialMastheadProfiles;

use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\config\Config;

class EditorialMastheadProfilesPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && Config::getVar('general', 'installed')) {
            Hook::add('LoadHandler', [$this, 'callbackLoadHandler']);
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
}

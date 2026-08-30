<?php
namespace APP\plugins\generic\editorialMastheadProfiles;

use PKP\config\Config;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class EditorialMastheadProfilesPlugin extends GenericPlugin
{
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
     * Replace OJS's standard Editorial Masthead template with the plugin copy.
     *
     * The plugin template intentionally tracks the upstream OJS/PKP template and
     * only adds profile links around current masthead member names. This makes the
     * feature work without a journal-specific theme or core-template modification.
     */
    public function callbackTemplateDisplay($hookName, $args): bool
    {
        $template =& $args[1];
        if (!is_string($template)) {
            return false;
        }

        $normalizedTemplate = preg_replace('/^(tpl:|app:|core:)/', '', $template);
        $normalizedTemplate = ltrim((string) $normalizedTemplate, '/');

        if ($normalizedTemplate !== 'frontend/pages/editorialMasthead.tpl') {
            return false;
        }

        $template = $this->getTemplateResource('editorialMasthead.tpl');
        return false;
    }
}

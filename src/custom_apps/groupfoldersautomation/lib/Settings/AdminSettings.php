<?php
namespace OCA\GroupFoldersAutomation\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {

    /** @var IL10N */
    private $l;

    /** @var IURLGenerator */
    private $urlGenerator;

    /** @var IConfig */
    private $config;

    public function __construct(IConfig $config, IL10N $l) {
        $this->config = $config;
        $this->l = $l;
    }

    /**
     * @return TemplateResponse php template that should be loaded in the section
     */
    public function getForm(): TemplateResponse {
        $parameters = [
            //'this_config_var' => $this->config->getSystemValue('some_system_var', true),
        ];

        return new TemplateResponse('groupfoldersautomation', 'settings/index', $parameters, '');
    }

    /**
     * @return string ID of the section that should be used
     */
    public function getSection(): string {
        return 'groupfoldersautomation';
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the admin section. The forms are arranged in ascending order of the
     * priority values. It is required to return a value between 0 and 100.
     */
    public function getPriority(): int {
        return 91;
    }
}
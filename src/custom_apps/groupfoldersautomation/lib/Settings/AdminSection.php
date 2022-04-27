<?php
namespace OCA\GroupFoldersAutomation\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection {

    /** @var IL10N */
    private $l;

    /** @var IURLGenerator */
    private $urlGenerator;

    public function __construct(IL10N $l, IURLGenerator $urlGenerator) {
        $this->l = $l;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return string Image path of the displayed icon
     */
    public function getIcon(): string {
        return $this->urlGenerator->imagePath('core', 'actions/settings-dark.svg');
    }

    /**
     * @return string Internal ID of the section
     */
    public function getID(): string {
        return 'groupfoldersautomation';
    }

    /**
     * @return string Display name of the section
     */
    public function getName(): string {
        return $this->l->t('Group Folders Automation');
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
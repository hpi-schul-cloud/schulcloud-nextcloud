<?php

namespace OCA\Schulcloud\Controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;

/**
 * See https://github.com/nextcloud/server/blob/master/lib/public/AppFramework/ApiController.php
 */
class LogoutController extends ApiController {

    public function __construct($appName, IRequest $request) {
        parent::__construct(
            $appName,
            $request,
            'GET',
            '',
            1728000);
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function index() : array {
        return array('loggedIn');
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function logout() : array {
        \OC::$server->getUserSession()->logout();
        return array('Logged out!');
    }
}

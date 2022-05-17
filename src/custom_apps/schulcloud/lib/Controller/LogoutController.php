<?php

namespace OCA\Schulcloud\Controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;

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
     */
    public function index() : array {
        return array('loggedIn');
    }

    /**
     * @NoCSRFRequired
     */
    public function logout() : array {
        \OC::$server->getUserSession()->logout();
        return array('Logged out!');
    }
}

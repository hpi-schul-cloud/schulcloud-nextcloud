<?php
namespace OCA\Schulcloud\Controller;

use OCP\AppFramework\ApiController;
use OCP\IRequest;
use OCP\IUserSession;

class LogoutController extends ApiController {

    /** @var IUserSession */
    private $session;

    public function __construct($appName, IRequest $request, IUserSession $session) {
        parent::__construct(
            $appName,
            $request,
            'GET',
            '',
            1728000);
        $this->session = $session;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @UseSession
     */
    public function logout(): array {
        $this->session->logout();
        return array('message'=>'Logout successful');
    }
}
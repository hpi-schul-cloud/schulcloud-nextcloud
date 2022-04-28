<?php
$CONFIG = array (
    'skeletondirectory' => '',
    'profile.enabled' => false,
    'hide_login_form' => true,
    'auth.webauthn.enabled' => false,
    'allow_user_to_change_display_name' => false,
    'upgrade.disable-web' => false,
    'config_is_read_only' => true,
    "apps_paths" => [
        [
            "path"     => OC::$SERVERROOT . "/apps",
            "url"      => "/apps",
            "writable" => false,
        ],
        [
            "path"     => OC::$SERVERROOT . "/custom_apps",
            "url"      => "/custom_apps",
            "writable" => true,
        ],
    ],
);

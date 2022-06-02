<?php
$CONFIG = array(
    'objectstore' => array(
        'class' => '\\OC\\Files\\ObjectStore\\S3',
        'arguments' => array(
            'bucket' => 'nextcloud',
            'autocreate' => true,
            'key' => 'SecretKey',
            'secret' => 'SuperSecretKey',
            'hostname' => 'storage',
            'port' => 9001,
            'use_ssl' => false,
            'use_path_style' => true,
            'legacy_auth' => true
        )
    )
);

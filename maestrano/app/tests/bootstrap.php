<?php

define('TEST_ROOT', __DIR__);

// Dependency: php-saml
define('PHP_SAML_DIR', './../../lib/php-saml/src/OneLogin/Saml/');
require PHP_SAML_DIR . 'AuthRequest.php';
require PHP_SAML_DIR . 'Response.php';
require PHP_SAML_DIR . 'Settings.php';
require PHP_SAML_DIR . 'XmlSec.php';

// Dependency: mno-php/sso
define('MNO_PHP_SSO_DIR', './../../lib/mno-php/src/sso/');
require MNO_PHP_SSO_DIR . 'MnoSsoBaseUser.php';

// Set timezone
date_default_timezone_set('UTC');
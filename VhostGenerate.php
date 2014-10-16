<?php
namespace Vhosts\Nginx;

require_once(realpath(dirname(__FILE__)."/lib/VhostGenerator.php"));

try {
    $obj = new VhostGenerator($argv[1], require_once(realpath(dirname(__FILE__)."/config/config.php")));
    $obj->generate();
} catch (\Exception $e) {
    echo $e->getMessage();
    die;
}

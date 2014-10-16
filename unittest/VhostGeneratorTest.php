<?php
namespace Vhosts\Nginx;

require_once(realpath(dirname(__FILE__) . '/../lib/VhostGenerator.php'));

/**
 * Unit tests for VhostGenerator.php
 * @author Pramod Patil <pramodnitkmca@gmail.com>
 */
class VhostGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsExceptionRaisedForInvalidDomain() 
    {
        try {
            new VhostGenerator("-localhost.com", require(realpath(dirname(__FILE__)."/../config/config.php")));
        } catch (\InvalidArgumentException $e) {
            return;
        }
        
        $this->fail('An expected exception has not been raised.');
    }
    
    public function testIsExceptionRaisedForInvalidDomain2() 
    {
        try {
            new VhostGenerator("localhost.", require(realpath(dirname(__FILE__)."/../config/config.php")));
        } catch (\InvalidArgumentException $e) {
            return;
        }
        
        $this->fail('An expected exception has not been raised.');
    }

    public function testIsExceptionRaisedForInvalidPort() 
    {
        try {
            new VhostGenerator("localhost1.com", array('port'=>13.5,'doc_root'=>'/usr/share/nginx/html/','vhost_template_path'=>'/usr/share/nginx/html/nginx_vhost_generator/template/nginx_vhost_template.txt'));
        } catch (\InvalidArgumentException $e) {
            return;
        }
        
        $this->fail('An expected exception has not been raised.');
    }
    
    public function testIsExceptionRaisedForInvalidRoot() 
    {
        try {
            new VhostGenerator("localhost1.com", array('port'=>80,'doc_root'=>'/does/not/exist/','vhost_template_path'=>'/usr/share/nginx/html/nginx_vhost_generator/template/nginx_vhost_template.txt'));
        } catch (\InvalidArgumentException $e) {
            return;
        }
        
        $this->fail('An expected exception has not been raised.');
    }

    public function testIsExceptionRaisedForInvalidTemplatePath() 
    {
        try {
            new VhostGenerator("localhost1.com", array('port'=>80,'doc_root'=>'/usr/share/nginx/html/','vhost_template_path'=>'/does/not/exist.txt'));
        } catch (\InvalidArgumentException $e) {
            return;
        }
        
        $this->fail('An expected exception has not been raised.');
    }
    
    public function testGenerate() 
    {
        $config = require(realpath(dirname(__FILE__)."/../config/config.php"));
        $domain = "localhost1.com";
        $obj = new VhostGenerator($domain, $config);
        $obj->generate();

        $expected = file_get_contents($config['vhost_template_path']);
        $expected = str_replace('{{root}}', $config['doc_root'], $expected);
        $expected = str_replace('{{domain}}', $domain, $expected);
        $expected = str_replace('{{port}}', $config['port'], $expected);
        
        $actual = file_get_contents('/etc/nginx/conf.d/localhost1.com.conf');

        $this->assertEquals($expected, $actual);
    }
}

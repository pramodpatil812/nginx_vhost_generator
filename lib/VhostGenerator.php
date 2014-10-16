<?php
namespace Vhosts\Nginx;

require_once(realpath(dirname(__FILE__) . '/FileUtil.php'));
require_once(realpath(dirname(__FILE__) . '/DomainUtil.php'));

/**
 * Nginx virtual host generator.
 * @author Pramod Patil <pramodnitkmca@gmail.com>
 */
class VhostGenerator
{
    /** @var string $domain virtual host domain by which it will be accessed.*/
    private $domain;

    /** @var array $config holds the configuration array of document root and port. */
    private $config;

    /** Nginx config directory. */
    const CONFIG_DIR = "/etc/nginx/conf.d/";

    /** Nginx config file extension. */
    const CONFIG_EXTENSION = ".conf";
   
    /**
     * Constructor function for class.
     *
     * @param string $domain virtual host domain by which it will be accessed.
     * @param string $doc_root public directory for virtual host.
     * @throws InvalidArgumentException If domain file already exists.
     * @throws InvalidArgumentException If the parameter $config['doc_root'] is not a directory.
     * @throws InvalidArgumentException If the parameter $config['vhost_template_path'] is not a regular file.
     * @throws InvalidArgumentException If the parameter $config['port'] is not integer.
     * @throws InvalidArgumentException If the parameter $domain is not valid.
     */ 
    public function __construct($domain, array $config)
    {
        if (!DomainUtil::isValidDomain($domain)) {
            throw new \InvalidArgumentException("Domain name $domain is invalid. Please give valid name.");
        }
        
        if (FileUtil::isFileExists(self::CONFIG_DIR.$domain.".conf")) {
            throw new \InvalidArgumentException("Virtual host $domain already exists. Please give another name.");
        }

        if (!FileUtil::isDirectory($config['doc_root'])) {
            throw new \InvalidArgumentException("Document root ".$config['doc_root']." is not valid configuration.");
        }
        
        if (!FileUtil::isFileExists($config['vhost_template_path'])) {
            throw new \InvalidArgumentException("Virtual host template name ".$config['vhost_template_path']." is not valid configuration.");
        }
        
        if (!is_int($config['port'])) {
            throw new \InvalidArgumentException("Port number must be integer. ".$config['vhost_template_path']." is not valid configuration.");
        }


        $this->domain = $domain;
        $this->config = $config;
    }
   
    /**
     * Read Nginx virtual host template from file.
     *
     * @return string static template for Nginx virtual host.
     */  
    private function getTemplate()
    {
        return FileUtil::readFile($this->config['vhost_template_path']);
    }
    
    /**
     * Generate virtual host.
     *
     * @throws InvalidArgumentException If Nginx configuration test fails.
     * @throws InvalidArgumentException If Nginx reload fails.
     * @return void.
     */
    public function generate()
    {
        if (($vhost = $this->getTemplate()) === false) {
            throw new \InvalidArgumentException("Vhost template reading failed.");
        }

        $vhost = str_replace('{{root}}', $this->config['doc_root'], $vhost);
        $vhost = str_replace('{{domain}}', $this->domain, $vhost);
        $vhost = str_replace('{{port}}', $this->config['port'], $vhost);
        
        $vhost_file = self::CONFIG_DIR.$this->domain.self::CONFIG_EXTENSION;

        if (FileUtil::writeToFile($vhost_file, $vhost) === false) {
            throw new \InvalidArgumentException("Writing to vhost file failed.");
        }

        if (!$this->validateConfig()) {
            //delete vhost file if validateconfig fails to prevent errors in next reload.
            unlink($vhost_file);

            throw new \InvalidArgumentException("Config test failed. Please check your configuration.");
        }

        if (!$this->reloadNginx()) {
            //delete vhost file if reload fails to prevent errors in next execution of this script.
            unlink($vhost_file);
            
            throw new \InvalidArgumentException("Unable to reload Nginx. Please check you configuration.");
        }
    }

    /**
     * Validates Nginx configuration.
     *
     * @return true, if configuration is ok, false otherwise.
     */
    private function validateConfig()
    {
        exec("nginx -t", $output, $status);

        //shell command returns 0 when successful, hence negating
        return !$status;
    }

    /**
     * Reload Nginx configuration. Use 'reload' instead of 'restart' as former will wait for the open connections to finish.
     *
     * @return true, if reload is successful, false otherwise.
     */
    private function reloadNginx()
    {
        exec("service nginx reload", $output, $status);

        //shell command returns 0 when successful, hence negating
        return !$status;
    }

    /**
     * destructor function for class.
     */
    public function __destruct()
    {
    }
}

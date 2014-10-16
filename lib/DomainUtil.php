<?php
namespace Vhosts\Nginx;

/**
 * Contains utility functions for domain validation.
 * @author Pramod Patil <pramodnitkmca@gmail.com>
 */
class DomainUtil
{
    /**
     * Check if given domain is valid or not.
     * @param string $domain domain name
     * @return boolean True, if domain is valid, false otherwise.
     */
    public static function isValidDomain($domain)
    {
        /*
        domain validation rules:
        1. Characters should only be a-z | A-Z | 0-9 and period(.) and dash(-)
        2. The domain name part should not start or end with dash (-) (e.g. -google-.com)
        3. The domain name part should be between 1 and 63 characters long.
        */
        if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $domain)) {
            return true;
        }
        
        return false;
    }
}

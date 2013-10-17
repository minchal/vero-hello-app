<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Action;

use Vero\Web;

class Error extends Web\Action\ExceptionHandler
{
    protected function getTemplate(\Exception $e)
    {
        // use admin template only if exists
        if (file_exists($this -> get('app') -> path('resources/views/exception/admin.twig'))) {
            if (strpos($this -> get('request') -> query, 'admin') === 0) {
                if ($this -> get('acl') -> check('admin')) {
                    return 'exception/admin.twig';
                }

                return 'exception/admin_guest.twig';
            }
        }

        return 'exception/main.twig';
    }
}

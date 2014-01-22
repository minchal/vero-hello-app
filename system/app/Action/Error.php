<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Action;

use Vero\Web;

class Error extends Web\Action\ExceptionHandler
{
    public function getExceptionResponseBody(\Exception $e)
    {
        $response = parent::getExceptionResponseBody($e);

        if ($e instanceof Web\Exception\AccessDenied &&
            class_exists('App\Entity\User\User') &&
            $this -> get('auth') -> getUser() -> isGuest()
        ) {
            $response -> message = $this -> i18ng(
                'access denied guest',
                [$this -> url('user/login') -> setGet('return', $this -> addReturnUrl())]
            );
        }

        return $response;
    }

    protected function getExceptionTemplate(\Exception $e)
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

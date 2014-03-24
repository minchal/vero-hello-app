<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Action;

use Vero\Web;

class Error extends Web\ExceptionHandler
{
    public function getExceptionResponseBody(\Exception $e)
    {
        $response = parent::getExceptionResponseBody($e);

        if ($e instanceof Web\Exception\AccessDenied &&
            class_exists('App\Entity\User\User') &&
            $this -> get('auth') -> getUser() -> isGuest()
        ) {
            $url = $this -> url($this -> isPanelRequest() ? 'user/admin/login' : 'user/login')
                -> setGet('return', $this -> addReturnUrl());

            $response -> message = $this -> i18ng('access denied guest', [$url]);
        }

        return $response;
    }

    protected function getExceptionTemplate(\Exception $e)
    {
        if ($this -> isPanelRequest()) {
            return 'exception/panel.twig';
        }

        return 'exception/main.twig';
    }

    protected function isPanelRequest()
    {
        // use panel templates and actions only if panel is installed
        return class_exists('App\Action\System\Overview') && strpos($this -> get('request') -> query, 'panel') === 0;
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Factory;
use Vero\View\Twig;

class TemplatingFactory extends Factory
{
    public function get($tpl = null, array $data = [])
    {
        // if no template name specified,
        // try to guess file name from router RouteID
        if (is_array($tpl) && !$data || !$tpl) {
            $data = (array) $tpl;
            $tpl = $this -> container -> get('request') -> action . '.twig';
        }

        $view = new Twig($tpl, $data);
        $view -> setContainer($this -> container);
        return $view;
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\Services\I18n as VeroI18n;

class I18n extends VeroI18n
{
    protected function create(Container $c)
    {
        $i18n = parent::create($c);
        
        if ($c -> has('request')) {
            $ses = $c -> get('session');

            if ($ses -> lang) {
                $i18n -> chooseLang([$ses -> lang => 1]);
            }
        }
        
        return $i18n;
    }
}

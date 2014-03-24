<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 * @see http://docs.jquery.com/Plugins/Validation/Methods/remote#options
 */

namespace App\Action;

use Vero\Web;
use Vero\Validate\Validator;
use Vero\Validate\RemotableContainerInterface;

class Validate extends Web\Action
{
    public function run(Web\Request $request)
    {
        $em = $this -> em();

        $class = 'App\VFC\\' . str_replace('.', '\\', $request -> entity);

        if (!class_exists($class)) {
            throw $this -> notFound();
        }

        $vfc = new $class($em, $request -> id);

        if (!$vfc instanceof RemotableContainerInterface) {
            throw $this -> notFound();
        }

        // first key from GET array
        $get = $request -> get();
        reset($get);
        $field = key($get);

        if (!$vfc -> canRemoteCheck($field)) {
            throw $this -> notFound();
        }

        $validator = new Validator($get);
        $validator -> mapContainer($vfc, [$field]);

        if ($validator -> isValid()) {
            return $this -> json(true);
        }

        return $this -> json($this -> i18n($validator -> error($field)));
    }
}

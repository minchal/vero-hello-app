<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Widget;

use Vero\View\Widget\Widget;

class Flash extends Widget
{
    public $bag;

    protected function init()
    {
        $session = $this -> container -> get('session');

        if ($session -> hasBag('flash')) {
            $this -> bag = $session -> getBag('flash');
        }
    }

    public function all()
    {
        if ($this -> bag) {
            return $this -> bag -> clear();
        }

        return [];
    }

    public function get($type)
    {
        if ($this -> bag) {
            return $this -> bag -> delete($type);
        }

        return null;
    }

    public function has($type)
    {
        if ($this -> bag) {
            return $this -> bag -> has($type);
        }

        return false;
    }
}

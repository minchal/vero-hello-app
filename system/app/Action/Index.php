<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Action;

use Vero\Web;

class Index extends Web\Action\Basic
{
    public function run(Web\Request $req)
    {
        return $this -> render();
    }
}

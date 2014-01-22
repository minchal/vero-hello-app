<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Services\Core;

use Vero\DependencyInjection\Factory;
use Vero\Vendor\Swift\JsonTemplate;
use Twig_Loader_String;

class MailTemplateFactory extends Factory
{
    public function get($tpl = null)
    {
        $c = $this -> container;
        $conf = $c -> get('config');

        $twig = $c -> get('twig');
        $twig -> setLoader(new Twig_Loader_String());

        $mailer = $c -> get('mailer');

        $mail = new JsonTemplate($mailer, $twig);

        $mail -> setFrom($conf -> get('mail.from', 'no-reply@localhost'));

        if ($tpl) {
            $mail -> setTemplate(
                $c -> get('app') -> path('resources/mail/' . $tpl . '.json')
            );
        }

        return $mail;
    }
}

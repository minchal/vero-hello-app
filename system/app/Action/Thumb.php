<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Action;

use Vero\Web;
use Vero\View\Attachment;
use Vero\Filesystem\File;
use Vero\Filesystem\Directory;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Thumb extends Web\Action
{
    public function run(Web\Request $req)
    {
        $upload = realpath($this -> get('app') -> path('upload'));
        $file = new File($upload . '/' . $req -> get('p'));

        if (!$file -> in($upload) || !$file -> isImage()) {
            throw $this -> notFound();
        }

        list(, $format) = explode('/', $file -> mimeType());
        $width = max(1, (int) $req -> get('w', 200));
        $height = max(1, (int) $req -> get('h', $width));

        $hash = md5(implode('_', [$file -> getPathname(), $req -> get('f'), $width, $height]));

        $cache = $this -> get('app') -> path(
            'var/thumbs/' . substr($hash, 0, 2) . '/' . substr($hash, 2) . '.' . $format
        );

        if (!file_exists($cache) || filemtime($cache) < $file -> getMTime()) {
            Directory::ensure(dirname($cache));

            $filter = $this -> getFilter($req -> get('f'));
            $thumb = $filter(
                $this -> get('imagine') -> open($file -> getPathname()),
                $width,
                $height
            );
            $thumb -> save($cache);
        }

        $response = $this -> response(new Attachment($cache, null, false));
        $response -> header('Content-Type', $file -> mimeType());
        return $response;
    }

    private function getFilter($name)
    {
        $filters = [
            '' => function ($image, $w, $h) {
                return $image -> thumbnail(new Box($w, $h), ImageInterface::THUMBNAIL_INSET);
            },
            'crop' => function ($image, $w, $h) {
                return $image -> thumbnail(new Box($w, $h), ImageInterface::THUMBNAIL_OUTBOUND);
            },
        ];

        if (!isset($filters[$name])) {
            throw $this -> notFound();
        }

        return $filters[$name];
    }
}

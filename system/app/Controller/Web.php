<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace App\Controller;

use Vero\Web\Controller as VeroController;
use Vero\Web\Request;

/**
 * {@inheritdoc}
 */
class Web extends VeroController
{
    /**
     * Remove _pjax GET param for default URL.
     * 
     * {@inheritdoc}
     */
    protected function getRequestParams(Request $request, $query, $id, $params)
    {
        $get = $request -> get();
        unset($get['_pjax']);
        
        return $params + [
            'query' => $query,
            'action' => $id,
            'url' => $this -> container -> get('router') -> url($id, $params) -> setGet($get)
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    protected function prepareResponse($response)
    {
        $response = parent::prepareResponse($response);
        
        $request = $this -> container -> get('request');
        
        if ($request -> server('HTTP_X_PJAX')) {
            $response -> header('X-PJAX-URL', (string) $request -> url);
        }
        
        return $response;
    }
}

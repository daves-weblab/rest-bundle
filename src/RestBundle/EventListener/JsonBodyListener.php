<?php

namespace DavesWeblab\RestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Eventlistener which appends a json request body to the request
 * attributes if possible.
 *
 * Class JsonBodyListener
 *
 * @package DWLRestBundle\EventListener
 * @author David Riedl <daves.weblab@gmail.com>
 */
class JsonBodyListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        try {
            $jsonBody = json_decode($request->getContent(), true);

            foreach ($jsonBody as $key => $value) {
                $request->attributes->set($key, $value);
            }
        } catch (\Exception $e) {
            // body was not json format, ignore
        }
    }
}
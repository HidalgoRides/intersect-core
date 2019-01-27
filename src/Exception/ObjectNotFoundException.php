<?php

namespace Intersect\Core\Exception;

class ObjectNotFoundException extends \Exception {

    public function __construct($objectType, array $messageTokens = [])
    {
        $message = $objectType . ' not found.';

        if (count($messageTokens) > 0)
        {
            $message .= ' [';
            foreach ($messageTokens as $key => $value)
            {
                $message .= $key . ': ' . $value;
            }
            $message .= ']';
        }

        parent::__construct($message);
    }

}
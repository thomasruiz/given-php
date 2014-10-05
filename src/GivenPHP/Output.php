<?php

namespace GivenPHP;

class Output
{

    const RED = '[31m';

    const BLUE = '[34m';

    const GREEN = '[32m';

    const WHITE = '[0m';

    /**
     * @param string $message
     * @param string $color
     */
    public static function message($message, $color = null)
    {
        if ($color !== null) {
            echo chr(27) . $color;
        }

        echo $message;
        echo chr(27) . Output::WHITE;
    }
}

<?php

declare(strict_types=1);

namespace Leaf;

use DateTime;
use Leaf\Date\Utils;

/**
 * Leaf Date
 * ----------------------
 * Quick date/time manipulation with Leaf
 *
 * @author Michael Darko
 * @since 1.1.0
 */
class Date
{
    /**Date entered by a user */
    protected string $userDate;

    /**Parsed user date */
    protected string $parsedDate;

    /**PHP datetime instance */
    protected DateTime $date;

    /**Date format entered by a user */
    protected string $userFormat;

    public function __construct()
    {
        $this->tick();
        $this->format();
    }

    /**
     *
     */
    public function tick(string $userDate = 'now', string $userTimeZone = null): Date
    {
        $this->userDate = $userDate;
        $this->date = new DateTime(str_replace('/', '-', $userDate));

        if ($userTimeZone) {
            $this->setTimezone($userTimeZone);
        }

        return $this;
    }

    /**
     * Set default date timezone
     */
    public function setTimezone(String $timezone = "Africa/Accra")
    {
        $this->date->setTimezone(new \DateTimeZone($timezone));

        return $this;
    }

    /**
     * Get the formatted date according to the string of tokens passed in.
     */
    public function format(string $format = 'c'): string
    {
        $this->userFormat = Utils::formatToPHP($format);
        $this->parsedDate = $this->date->format($this->userFormat);

        return $this->parsedDate;
    }
}

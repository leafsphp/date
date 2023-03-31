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
     * Add a duration to the current date
     */
    public function add($duration, string $interval = null): Date
    {
        $this->date->modify($interval ? "$duration $interval" : $duration);

        return $this;
    }

    /**
     * Subtract a duration to the current date
     */
    public function subtract($duration, string $interval = null): Date
    {
        return $this->add($interval ? "-$duration $interval" : '-' . $duration);
    }

    /**
     * Get the start of a time unit
     */
    public function startOf(string $unit): Date
    {
        $units = [
            'year' => 'Y-01-01 00:00:00',
            'month' => 'Y-m-01 00:00:00',
            'week' => 'Y-m-d 00:00:00',
            'day' => 'Y-m-d 00:00:00',
            'hour' => 'Y-m-d H:00:00',
            'minute' => 'Y-m-d H:i:00',
            'second' => 'Y-m-d H:i:s',
        ];

        $this->date->modify(date(
            $units[$unit],
            $unit === 'week' ?
                strtotime("this week", $this->date->getTimestamp()) :
                $this->date->getTimestamp()
        ));

        return $this;
    }

	/**
	 * Get the end of a time unit
	 */
	public function endOf(string $unit): Date
	{
		$units = [
			'year' => 'Y-12-31 23:59:59',
			'month' => 'Y-m-t 23:59:59',
			'week' => 'Y-m-d 23:59:59',
			'day' => 'Y-m-d 23:59:59',
			'hour' => 'Y-m-d H:59:59',
			'minute' => 'Y-m-d H:i:59',
			'second' => 'Y-m-d H:i:s',
		];

		$this->date->modify(date(
			$units[$unit],
			$unit === 'week' ?
				date_add(date_create(date('Y-m-d', strtotime("this week", $this->date->getTimestamp()))), date_interval_create_from_date_string('6 days'))->getTimestamp() :
				$this->date->getTimestamp()
		));

		return $this;
	}

    /**
     * Get the formatted date according to the string of tokens passed in.
     */
    public function format(string $format = 'c'): string
    {
		$matches = [
			'YY' => 'y',
			'YYYY' => 'Y',
			'M' => 'n',
			'MM' => 'm',
			'MMM' => 'M',
			'MMMM' => 'F',
			'D' => 'j',
			'DD' => 'd',
			'd' => 'w',
			'dd' => 'D',
			'ddd' => 'D',
			'dddd' => 'l',
			'H' => 'G',
			'HH' => 'H',
			'h' => 'g',
			'hh' => 'h',
			'a' => 'a',
			'A' => 'A',
			'm' => 'i',
			'mm' => 'i',
			's' => 's',
			'ss' => 's',
			'SSS' => 'u',
			'Z' => 'Z',
			'T' => '\T',
		];

        $this->userFormat = preg_replace_callback('/\[([^\]]+)]|Y{1,4}|T|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/', function ($match) use ($matches) {
			if (strpos($match[0], '[') === 0) {
				return preg_replace_callback('/\[(.*?)\]/', function ($matched) {
					return preg_replace("/(.)/", "\\\\$1", $matched[1]);
				}, $match[0]);
			}

			return $matches[$match[0]] ?? $match[0];
		}, $format);

        $this->parsedDate = $this->date->format($this->userFormat);

        return $this->parsedDate;
    }

	/**
	 * Returns the string of relative time from a date.
	 */
	public function from($date = 'now', $valueOnly = false): string
	{
		$interval = $this->date->diff(new DateTime(str_replace('/', '-', $date)));

		$years = $interval->format('%y');
		$months = $interval->format('%m');
		$days = $interval->format('%d');
		$hours = $interval->format('%h');
		$minutes = $interval->format('%i');

		if ($years > 0) {
			$relativeDate = $years . ' year' . ($years === 1 ? '' : 's') . ($valueOnly ? '' : ($years > 0 && $months > 0 ? ' and ' : '') . ($months > 0 ? ($months . ' month' . ($months === 1 ? '' : 's')) : ''));
		} else if ($months > 0) {
			$relativeDate = $months . ' month' . ($months === 1 ? '' : 's') . ($valueOnly ? '' : ($months > 0 && $days < 20 ? ' and ' : '') . ($days < 20 ? ($days . ' day' . ($days === 1 ? '' : 's')) : ''));
		} else if ($days > 0) {
			$relativeDate = $days . ' day' . ($days === 1 ? '' : 's') . ($valueOnly ? '' : ($days > 0 && $hours > 0 ? ' and ' : '') . ($hours > 0 ? ($hours . ' hour' . ($hours === 1 ? '' : 's')) : ''));
		} else if ($hours > 0) {
			$relativeDate = $hours . ' hour' . ($hours === 1 ? '' : 's') . ($valueOnly ? '' : ($hours > 0 && $minutes > 0 ? ' and ' : '') . ($minutes > 0 ? ($minutes . ' minute' . ($minutes === 1 ? '' : 's')) : ''));
		} else if ($minutes > 0) {
			$relativeDate = $minutes . ' minute' . ($minutes === 1 ? '' : 's');
		} else {
			$relativeDate = 'less than a minute';
		}

		if ($valueOnly) {
			return $relativeDate;
		}

		return $relativeDate
			. ($this->date > new DateTime() ? ' from now' : ' ago');
	}

	/**
	 * Returns the string of relative time from now.
	 */
	public function fromNow($valueOnly = false): string
	{
		return $this->from('now', $valueOnly);
	}

	/**
	 * Returns the string of relative time from now.
	 */
	public function toNow($valueOnly = false): string
	{
		return $this->fromNow($valueOnly);
	}

	/**
	 * Return as PHP DateTime object
	 */
	public function toDateTime(): DateTime
	{
		return $this->date;
	}

	/**
	 * Return as PHP DateTime object
	 */
	public function toDateTimeString(): string
	{
		return $this->date->format('Y-m-d H:i:s');
	}

	/**
	 * Return as PHP DateTime object
	 */
	public function toDateString(): string
	{
		return $this->date->format('Y-m-d');
	}

	/**
	 * Return as PHP DateTime object
	 */
	public function toTimeString(): string
	{
		return $this->date->format('H:i:s');
	}

	/**
	 * Return as PHP DateTime object
	 */
	public function toIso8601String(): string
	{
		return $this->date->format('Y-m-d\TH:i:sO');
	}
}

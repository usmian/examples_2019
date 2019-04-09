<?php

namespace IntervalsFinder;

class TimeSlot
{
    /**
     * TimeSlot constructor.
     * @param $start
     * @param $end
     */
    public function __construct($start, $end)
    {
        $this->start = new \DateTime($start);
        $this->end = new \DateTime($end);
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;
}
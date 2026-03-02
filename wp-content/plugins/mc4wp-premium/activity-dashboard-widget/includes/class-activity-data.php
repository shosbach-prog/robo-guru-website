<?php

class MC4WP_ADW_Activity_Data
{
    /**
     * @var array
     */
    protected $raw_data;

    /**
     * @param array $raw_data
     * @param int $days
     */
    public function __construct(array $raw_data, $days)
    {
        // get last n days of data
        $this->raw_data = array_slice($raw_data, 0 - $days);
    }

    /**
     * @return array
     */
    public function to_array()
    {
        $subs = [];
        $unsubs = [];

        foreach ($this->raw_data as $day_object) {
            $subs[] = [
                'x' => $day_object->day,
                'y' => $day_object->subs,
            ];
            $unsubs[] = [
                'x' => $day_object->day,
                'y' => -$day_object->unsubs,
            ];
        }

        return [ array_reverse($subs), array_reverse($unsubs) ];
    }
}

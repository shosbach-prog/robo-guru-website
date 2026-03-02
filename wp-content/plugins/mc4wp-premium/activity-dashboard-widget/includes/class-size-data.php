<?php

class MC4WP_ADW_Size_Data
{
    /**
     * @var array
     */
    protected $activity_data;

    /**
     * @var array
     */
    protected $datasets = [];

    /**
     * @var int
     */
    protected $current_list_size = 0;

    /**
     * @var int
     */
    protected $days = 90;

    /**
     * @param array $raw_data
     * @param int $current_list_size
     * @param int $days
     */
    public function __construct(array $raw_data, $current_list_size, $days)
    {
        $this->current_list_size = $current_list_size;
        $this->days = $days;
        $this->activity_data = $raw_data;
        $this->calculate();
    }

    /**
     * Calculate list size data from activity data
     */
    public function calculate()
    {
        $data = $this->activity_data;

        // limit to number of days
        if (count($data) > $this->days) {
            $data = array_slice($data, 0 - $this->days);
        }

        // reverse array if needed, we need to start today and work our way down
        if (count($data) > 1 && strtotime($data[0]->day) < strtotime($data[1]->day)) {
            $data = array_reverse($this->activity_data);
        }

        $size_at_day = $this->current_list_size;

        $size = [];
        foreach ($data as $day_object) {
            $size_at_day = $size_at_day - $day_object->subs + $day_object->unsubs;
            $size[] = [
                'x' => $day_object->day,
                'y' => $size_at_day
            ];
        }
        $this->datasets = [ array_reverse($size) ];
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return $this->datasets;
    }
}

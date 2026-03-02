<?php

class MC4WP_RGB_Color
{
    /**
     * @var int
     */
    public $r;

    /**
     * @var int
     */
    public $g;

    /**
     * @var int
     */
    public $b;

    /**
     * @var string
     */
    public $hex;

    /**
     * @param string $color Hexadecimal color value, eg #FFCC00
     */
    public function __construct($color)
    {
        // create hex string of 6 chars
        $hex = str_replace('#', '', $color);

        // check for shorthand notation, eg #FC0
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }

        $this->hex = '#' . $hex;

        // Get decimal values
        $this->r = (int) hexdec(substr($hex, 0, 2));
        $this->g = (int) hexdec(substr($hex, 2, 2));
        $this->b = (int) hexdec(substr($hex, 4, 2));
    }

    /**
     * @param int $percentage Relative amount to darken the base color. Should be a value between 0 and 100.
     *
     * @return string
     */
    public function darken($percentage)
    {
        $amount = (int) ((float) $percentage * 255.0 / 100.0);

        $r = max(0, min(255, $this->r - $amount));
        $g = max(0, min(255, $this->g - $amount));
        $b = max(0, min(255, $this->b - $amount));

        $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
        $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
        $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

        return '#' . $r_hex . $g_hex . $b_hex;
    }

    /**
     * @param int $percentage Relative amount to lighten the base color. Should be a value between 0 and 100.
     *
     * @return string
     */
    public function lighten($percentage)
    {
        $amount = (int) ((float) $percentage / 100.0 * 255.0);

        $r = max(0, min(255, $this->r + $amount));
        $g = max(0, min(255, $this->g + $amount));
        $b = max(0, min(255, $this->b + $amount));

        $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
        $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
        $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
        return '#' . $r_hex . $g_hex . $b_hex;
    }

    /**
     * @return float
     */
    public function lightness()
    {
        $avg = ((float) ($this->r + $this->g + $this->b) / 3.0);
        return $avg / 255.0 * 100.0;
    }
}

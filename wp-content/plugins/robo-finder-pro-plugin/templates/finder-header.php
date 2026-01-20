<?php
/**
 * Robo Finder Header Template
 * You can edit this file safely to change the Finder heading/subline.
 *
 * Available variables:
 * - $title (string)
 * - $subtitle (string)
 */
$title = __('Robo-Finder - Finde den passenden Roboter für deinen Einsatz','robo-finder-pro');
$subtitle = isset($subtitle) ? $subtitle : __('Beantworte ein paar Fragen – wir filtern vor und prüfen die Machbarkeit für dich.','robo-finder-pro');
?>
<h1 class="rf-title"><?php echo esc_html( $title ); ?></h1>
<p class="rf-sub"><?php echo esc_html( $subtitle ); ?></p>

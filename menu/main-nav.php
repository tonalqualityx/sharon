<?php
defined('ABSPATH') or die('No script kiddies please!'); //For security

class ind_Walker extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth=0, $args=array(), $id = 0) {
    	$object = $item->object;
    	$type = $item->type;
    	$title = $item->title;
    	$description = $item->description;
        $permalink = $item->url;

        $output .= "<li class='ind-nav-item " . implode(" ", $item->classes) . "'>";
        if ($permalink && $permalink != '#') {
            if (strpos($permalink, home_url()) !== false || $permalink == "/"){
                $target = '';
            }else{
                $target = ' target="_blank" ';
            }
            $output .= '<a href="' . $permalink . '" ' . $target . '>';
        }
        $output .= $title;
        if ($permalink && $permalink != '#') {
            $output .= '</a>';
        }
    }

}
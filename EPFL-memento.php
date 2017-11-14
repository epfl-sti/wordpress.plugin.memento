<?php
/*
Plugin Name: EPFL Memento shortcodes and function
Plugin URI: https://github.com/epfl-sti/wordpress.plugin.memento
Description: provides a shortcode and a function to dispay results from Memento
Version: 0.9
Author: Loïc Humbert
Author URI: https://people.epfl.ch/loic.humbert?lang=en
License: Copyright (c) 2017 Ecole Polytechnique Federale de Lausanne, Switzerland
*/

function epfl_memento_get_rss_content($rss_url)
{

  $curl = curl_init();
  curl_setopt_array($curl, Array(
    CURLOPT_URL            => $rss_url, // 'https://memento.epfl.ch/feeds/rss/?lang=en&period=upcoming7days&memento=sti',
    CURLOPT_USERAGENT      => 'jawpb',  // just another wp blog
    CURLOPT_TIMEOUT        => 120,
    CURLOPT_CONNECTTIMEOUT => 30,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_ENCODING       => 'UTF-8'
  ));

  $data = curl_exec($curl);
  curl_close($curl);
  return simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
}

function epfl_memento_gen_url($memento_atts)
{
  $url  = esc_attr($memento_atts['url']) . '?';
  $url .= 'lang=' . esc_attr($memento_atts['lang']);
  $url .= '&period=' . esc_attr($memento_atts['period']);
  $url .= '&memento=' . esc_attr($memento_atts['scope']);
  $url .= (esc_attr($memento_atts['category']) !== '') ?
          ('&category=' . esc_attr($memento_atts['category'])) : '';

  return $url;
}

/**
 * Main logic
 **/
function epfl_memento_wp_shortcode($atts, $content=null, $tag='') {
  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);

  // override default attributes with user attributes
  $memento_atts = shortcode_atts(['number'   => 10,
                                  'scope'    => 'epfl', // sti, sv, epfl etc...
                                  'lang'     => 'en',
                                  'period'   => 'upcoming',
                                  'category' => '',     // CONF, COURS, EXPO etc...
                                  'tmpl'     => 'full', // full, short, widget
                                  'url'      => 'https://memento.epfl.ch/feeds/rss/', // https://help-memento.epfl.ch/page-76077-en.html
                               ], $atts, $tag);

  $max = esc_attr($memento_atts['number']);
  $tmpl = esc_attr($memento_atts['tmpl']);
  $rss_xml = epfl_memento_get_rss_content(epfl_memento_gen_url($memento_atts));

  switch ($tmpl) {
    default:
    case 'example':
      include 'important_fields_example.php'; // Example function inside
      $display_html = epfl_memento_display_example($rss_xml, $max);
      break;
  }
  return $display_html ? $display_html : false;
}

add_shortcode('memento', 'epfl_memento_wp_shortcode');
?>

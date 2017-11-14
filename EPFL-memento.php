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
include 'functions.php';

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

function epfl_memento_get_event($rss_xml, $link)
{
  $ns = $rss_xml->channel->getNamespaces(true);

  foreach ($rss_xml->channel->item as $item) {
    if ($item->link == $link) {
      $epfl = $item->children($ns['epfl']);
      $event = array();
      $event['startDate'] = (string) $epfl->startDate;
      $event['endDate'] = (string) $epfl->endDate;
      $event['title'] = (string) $item->title;
      $event['link'] = (string) $item->link;
      $event['location'] = (string) $epfl->location;
      $event['description'] = trim_br((string) $item->description);
      return $event;
    }
  }
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
function epfl_memento_event_calendar_wp_shortcode($atts)
{
  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);

  // override default attributes with user attributes
  $memento_event_atts = shortcode_atts(['date'    => '00-00-0000',
                                        'link'    => '',     // 	http://memento.epfl.ch/event/xxx
                                       ], $atts);

  $url = 'https://memento.epfl.ch/feeds/rss/?period=' . str_replace('-', '', esc_attr($memento_event_atts['date'])) . '-' . str_replace('-', '', esc_attr($memento_event_atts['date']));

  $rss_xml = epfl_memento_get_rss_content($url);

  return epfl_memento_get_event($rss_xml, esc_attr($memento_event_atts['link']));
}

function epfl_memento_multi_wp_shortcode($atts) {
  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);

  // override default attributes with user attributes
  $memento_multi_atts = shortcode_atts(['number'   => 10,
                                  'scope'    => 'epfl', // sti, sv, epfl etc...
                                  'lang'     => 'en',
                                  'period'   => 'upcoming',
                                  'category' => '',     // CONF, COURS, EXPO etc...
                                  'tmpl'     => 'full', // full, short, widget
                                  'url'      => 'https://memento.epfl.ch/feeds/rss/', // https://help-memento.epfl.ch/page-76077-en.html
                               ], $atts);

  $max = esc_attr($memento_multi_atts['number']);
  $tmpl = esc_attr($memento_multi_atts['tmpl']);
  $rss_xml = epfl_memento_get_rss_content(epfl_memento_gen_url($memento_multi_atts));

  switch ($tmpl) {
    default:
    case 'example':
      include 'important_fields_example.php'; // Example function inside
      $display_html = epfl_memento_display_example($rss_xml, $max);
      break;
  }
  return $display_html ? $display_html : false;
}

add_shortcode('memento_multi', 'epfl_memento_multi_wp_shortcode');
add_shortcode('memento_event', 'epfl_memento_event_calendar_wp_shortcode');
?>

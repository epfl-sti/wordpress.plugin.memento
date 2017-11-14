<?php

/*
Plugin Name: EPFL Memento shortcodes and function
Plugin URI: https://github.com/epfl-sti/EPFL-Memento
Description: provides a shortcode to dispay results from Memento
Version: 1.0
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

function epfl_memento_display_full($rss_xml, $max_number)
{
  $count=0;
  foreach ($rss_xml->channel->item as $item) {
    $creator = $item->children('dc', TRUE);
    $tmp .= '<h2>' . $item->title . '</h2>';
    $tmp .= '<p>Created: ' . $item->pubDate . '</p>';
    $tmp .= '<p>Author: ' . $creator . '</p>';
    $tmp .= '<p>' . $item->description . '</p>';
    $tmp .= '<p><a href="' . $item->link . '">Read more: ' . $item->title . '</a></p>';
    if ($count++ >= $max_number) break;
  }
  return $tmp;
}

function epfl_memento_display_short($rss_xml, $max_number)
{
  $count=0;
  $tmp = '';
  foreach ($rss_xml->channel->item as $item) {
    $creator = $item->children('dc', TRUE);
    $tmp .= '<h2>' . $item->title . '</h2>';
    $tmp .= '<p>Date: ' . $item->startDate . '</p>';
    if ($count++ >= $max_number) break;
  }
  return $tmp;
}

function epfl_memento_display_widget($rss_xml, $max_number)
{
  $count=0;
  foreach ($rss_xml->channel->item as $item) {
    $creator = $item->children('dc', TRUE);
    $tmp .= '<b>' . $item->title . '</b>';
    preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $item->description, $image);
    $tmp .= '<p><a href="' . $item->link . '" target="_blank"><img src="' . $image['src'] . '" title="' . $item->title . '" /></a></p>';
    if ($count++ >= $max_number) break;
  }
  return $tmp;
}

function epfl_memento_display_test($rss_xml, $max_number)
{
  $count = 0;
  foreach ($rss_xml->channel->item as $item) {
    var_dump($item);
    if ($count++ >= $max_number) break;
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
    case 'test':
      $display_html = epfl_memento_display_test($rss_xml, $max);
      break;
    case 'full':
      $display_html = epfl_memento_display_full($rss_xml, $max);
      break;
    case 'short':
      $display_html = epfl_memento_display_short($rss_xml, $max);
      break;
    case 'widget':
      $display_html = epfl_memento_display_widget($rss_xml, $max);
      break;
    case 'bootstrap-card':
      $display_html = epfl_memento_display_bootstrap_card($rss_xml, $max);
      break;
  }
  return $display_html ? $display_html : false;
}

add_shortcode('memento', 'epfl_memento_wp_shortcode');
?>

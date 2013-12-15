<?php

/**
 * @file template.php
 * @author griffinj@lafayette.edu
 * This file contains the primary theme hooks found within any given Drupal 7.x theme
 *
 * @todo Implement some Drupal theming hooks
 */

  // Includes functions to create Islandora Solr blocks.
require_once dirname(__FILE__) . '/includes/blocks.inc';
require_once dirname(__FILE__) . '/includes/forms.inc';
require_once dirname(__FILE__) . '/includes/menus.inc';

/**
 * Preprocess variables for page.tpl.php
 *
 * @see page.tpl.php
 */

function bootstrap_dss_islandora_dev_preprocess_page(&$variables) {

  // Add information about the number of sidebars.
  if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
    $variables['columns'] = 3;

  }
  elseif (!empty($variables['page']['sidebar_first'])) {
    $variables['columns'] = 2;
  }
  elseif (!empty($variables['page']['sidebar_second'])) {
    $variables['columns'] = 2;
  }
  else {
    $variables['columns'] = 1;
  }

  // Primary nav
  $variables['primary_nav'] = FALSE;
  if ($variables['main_menu']) {
    // Build links
    $variables['primary_nav'] = menu_tree(variable_get('menu_main_links_source', 'main-menu'));
    // Provide default theme wrapper function
    $variables['primary_nav']['#theme_wrappers'] = array('menu_tree__primary');
  }

  // Secondary nav
  $variables['secondary_nav'] = FALSE;
  if ($variables['secondary_menu']) {
    // Build links
    $variables['secondary_nav'] = menu_tree(variable_get('menu_secondary_links_source', 'user-menu'));
    // Provide default theme wrapper function
    $variables['secondary_nav']['#theme_wrappers'] = array('menu_tree__secondary');
  }

  // The "Contact Us" link
  $variables['contact_anchor'] = l(t('Contact Us'), '', array('attributes' => array('data-toggle' => 'lafayette-dss-modal',
										    'data-target' => '#contact',
										    'data-anchor-align' => 'false'),
							      'fragment' => ' ',
							      'external' => TRUE));

  // The "Log In" link
  //$variables['auth_anchor'] = l(t('Log In'), '', array('attributes' => array('data-toggle' => 'lafayette-dss-modal',
  /*
  $variables['auth_anchor'] = l('<div class="auth-icon"><img src="/sites/all/themes/bootstrap_dss_islandora_dev/files/UserIcon.png" /><span>Log In</span></div>', '', array('attributes' => array('data-toggle' => 'lafayette-dss-modal',
														    'data-target' => '#auth-modal',
																								  'data-width-offset' => '10px',
														    'data-height-offset' => '28px'),
											      'fragment' => ' ',
											      //'external' => TRUE));
											      'external' => TRUE,
											      'html' => TRUE
											      ));
  */

  $variables['auth_anchor'] = '<a data-toggle="lafayette-dss-modal" data-target="#auth-modal" data-width-offset="0px" data-height-offset="30px"><div class="auth-icon"><img src="/sites/all/themes/bootstrap_dss_islandora_dev/files/UserIcon.png" /><span>Log In</span></div></a>';

  //$variables['auth_anchor'] = '<a id="auth-modal-help" class="btn btn-default" data-content="Authentication help message." data-placement="bottom" data-toggle="popover" data-container=".share-control-container" title=""><div class="auth-icon"><img src="/sites/all/themes/bootstrap_dss_islandora_dev/files/UserIcon.png" /><span>Log In</span></div></a>';

  $variables['auth_anchor'] = '<a id="auth-modal-toggle" class="modal-toggle" data-placement="bottom" data-toggle="popover" data-container=".auth-container" data-original-title="" data-html="true" title=""><div class="auth-icon"><img src="/sites/all/themes/bootstrap_dss_islandora_dev/files/UserIcon.png" /><span>Log In</span></div></a>';

  // The "Log Out" link
  $variables['logout_anchor'] = l(t('Log Out'), 'user/logout');

  // The "Share" link
  //$variables['share_anchor'] = l(t('Share'), '', array('attributes' => array('data-toggle' => 'lafayette-dss-modal',
  /*
  $variables['share_anchor'] = l('<div class="share-icon"><img src="/sites/all/themes/bootstrap_dss_islandora_dev/files/ShareIcon.png" /><span>Share</span></div>', '', array('attributes' => array('data-toggle' => 'lafayette-dss-modal',
									     'data-target' => '#share-modal',
																								    'data-width-offset' => '10px',
									     'data-height-offset' => '28px'
									     ),
						       'fragment' => ' ',
						       //'external' => TRUE));
						       'external' => TRUE,
						       'html' => TRUE
						       ));
  */

  $variables['share_anchor'] = '<a data-toggle="lafayette-dss-modal" data-target="#share-modal" data-width-offset="10px" data-height-offset="28px"><div class="share-icon"><img src="/sites/all/themes/bootstrap_dss_islandora_dev/files/ShareIcon.png" /><span>Share</span></div></a>';

  $variables['share_anchor'] = '<a id="share-modal-toggle" class="modal-toggle" data-placement="bottom" data-toggle="popover" data-container=".share-container" data-original-title="" data-html="true" title=""><div class="share-icon"><img src="/sites/all/themes/bootstrap_dss_islandora_dev/files/ShareIcon.png" /><span>Share</span></div></a>';

  // Render thumbnails for authenticated users
  // By default, use a glyphicon
  $variables['user_picture'] = '<span class="button-auth-icon"></span>';

  if(user_is_logged_in()) {

    // For the user thumbnail
    global $user;
    $user_view = user_view($user);
    $variables['user_picture'] = drupal_render($user_view['user_picture']);
  }

  // Ensure that "home" always exists for the breadcrumbs
  if(empty($variables['breadcrumb'])) {

    $variables['breadcrumb'] = '<ul class="breadcrumb"><li>' . l(t('Home'), $variables['front_page']) . '</li></ul>';
  }

  // Work-around for the logo image
  $variables['dss_logo_image'] = theme_image(array('path' => drupal_get_path('theme', 'bootstrap_dss_islandora_dev') . '/files/dss_logo.png',
						   'alt' => t('digital scholarship services logo'),
						   'attributes' => array()));

}

/**
 * Implements template_preprocess_hybridauth_widget
 * @griffinj
 *
 */
function bootstrap_dss_islandora_dev_preprocess_hybridauth_widget(&$vars) {

  // Refactor
  $i = 0;
  foreach (hybridauth_get_enabled_providers() as $provider_id => $provider_name) {

    //$vars['providers'][$i] .= preg_replace('/(<\/span>)/', "</span><span>&nbsp;$provider_name</span>", $vars['providers'][$i]);
    //if(preg_match('/<span class="sharethis-anchor-wrapper"><\/span>/', $vars['providers'][$i])) {

    $vars['providers'][$i] = preg_replace('/(<\/span>)/', "</span><span>&nbsp;$provider_name</span>", $vars['providers'][$i]);
    $i++;
      //}
  }
}

/**
 * Implements template_preprocess_html
 *
 */
function bootstrap_dss_islandora_dev_preprocess_html(&$variables) {

  drupal_add_library('system', 'effects.drop');
  drupal_add_library('system', 'effects.slide');
}

/**
 * Template preprocess function for hybridauth_widget.
 */
/*
function template_preprocess_hybridauth_widget(&$vars, $hook) {

}
*/

function bootstrap_dss_islandora_dev_theme_registry_alter(&$registry) {

  $registry['hybridauth_widget']['file'] = 'template';
}

/**
 * Implements hook_theme().
 */
/*
function hybridauth_theme($existing, $type, $theme, $path) {
  return array(
    'hybridauth_admin_settings_providers_table' => array(
      'render element' => 'form',
      'file' => 'hybridauth.admin.inc',
    ),
    'hybridauth_widget' => array(
      'render element' => 'element',
      'template' => 'templates/hybridauth_widget',
      'file' => 'hybridauth.theme.inc',
    ),
}
*/

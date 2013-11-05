<?php
/*
Plugin Name: Min Calendar
Plugin URI: http://www.min-ker.com
Description: Add minimum calendar
Text Domain: min-calendar
Domain Path: /languages/
Author: Hiroshi Sawai
Author URI: http://www.min-ker.com
Version: 1.4.1
*/

/*  Copyright 2013  Hiroshi Sawai (email : info@info-town.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'MC_VERSION', '1.4.1' );
define( 'MC_REQUIRED_WP_VERSION', '3.5.1' );

if ( ! defined( 'MC_PLUGIN_BASENAME' ) ) {
    define( 'MC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'MC_PLUGIN_NAME' ) ) {
    define( 'MC_PLUGIN_NAME', trim( dirname( MC_PLUGIN_BASENAME ), '/' ) );
}
if ( ! defined( 'MC_PLUGIN_DIR' ) ) {
    define( 'MC_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
}
if ( ! defined( 'MC_PLUGIN_URL' ) ) {
    define( 'MC_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
}
if ( ! defined( 'MC_CALENDAR_STYLESHEET' ) ) {
    define( 'MC_CALENDAR_STYLESHEET' , MC_PLUGIN_DIR . '/includes/css/mincalendar.css' );
}
/* If you or your client hate to see about donation, set this value false. */
if ( ! defined( 'MC_SHOW_DONATION_LINK' ) ) {
    define( 'MC_SHOW_DONATION_LINK', true );
}
if ( ! defined( 'MC_ADMIN_READ_CAPABILITY' ) ) {
    define( 'MC_ADMIN_READ_CAPABILITY', 'edit_posts' );
}
if ( ! defined( 'MC_ADMIN_READ_WRITE_CAPABILITY' ) ) {
    define( 'MC_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages' );
}
if ( ! defined( 'MC_VERIFY_NONCE' ) ) {
    define( 'MC_VERIFY_NONCE', true );
}

require_once MC_PLUGIN_DIR  . '/class-main.php';
new MC_Main();

?>

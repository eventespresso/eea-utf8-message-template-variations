<?php
/*
  Plugin Name: Event Espresso - UTF8 Template Variation (EE4.5+)
  Plugin URI: http://www.eventespresso.com
  Description: The Event Espresso UTF8 Template Variation addons add the "Simple UTF8" template style for the Receipt and Invoice messages. This style supports UTF8 characters in the PDF. Compatible with Event Espresso 4.5 or higher
  Version: 1.0.0.rc.000
  Author: Event Espresso
  Author URI: http://www.eventespresso.com
  Copyright 2014 Event Espresso (email : support@eventespresso.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA
 *
 * ------------------------------------------------------------------------
 *
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package		Event Espresso
 * @ author			Event Espresso
 * @ copyright	(c) 2008-2014 Event Espresso  All Rights Reserved.
 * @ license		http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link				http://www.eventespresso.com
 * @ version	 	EE4
 *
 * ------------------------------------------------------------------------
 */
define( 'EE_UTF8_VERSION', '1.0.0.rc.000' );
define( 'EE_UTF8_PLUGIN_FILE',  __FILE__ );
function load_espresso_utf8() {
if ( class_exists( 'EE_Addon' )) {
	// utf8 version
	require_once ( plugin_dir_path( __FILE__ ) . 'EE_UTF8.class.php' );
	EE_UTF8::register_addon();
}
}
add_action( 'AHEE__EE_System__load_espresso_addons', 'load_espresso_utf8' );

// End of file espresso_utf8.php
// Location: wp-content/plugins/espresso-utf8/espresso_utf8.php

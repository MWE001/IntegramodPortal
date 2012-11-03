<?php
/***************************************************************************
 *                        blocks_imp_visit_counter.php
 *                            -------------------
 *   begin                : Saturday, March 20, 2004
 *   copyright            : (C) 2004 masterdavid - Ronald John David
 *   website              : http://phpbbintegramod.sourceforge.net
 *   email                : masterdavid@users.sourceforge.net
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

if(!function_exists(imp_visit_counter_block_func))
{
	function imp_visit_counter_block_func()
	{
		global $template, $lang, $board_config;
		$template->assign_vars(array(
			'VISIT_COUNTER' => sprintf($lang['Visit_counter_statement'], $board_config['visit_counter']+1, date('F d, Y', $board_config['board_startdate'])),
			'L_VISIT_COUNTER' =>$lang['Visit_counter']
			)
		);
	}
}

imp_visit_counter_block_func();
?>
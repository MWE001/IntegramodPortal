<?php
/**
*
* @package Anti-Spam ACP
* @copyright (c) 2008 EXreaction, Lithium Studios
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/*
* ignore
*/
if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

include_once($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_anti_spam_acp.' . $phpEx);
include($phpbb_root_path . 'includes/functions_anti_spam_acp.' . $phpEx);

$profile_fields = main_profile_fields($HTTP_POST_VARS); // function is in functions_anti_spam_acp.php
$lang_profile_fields = admin_profile_fields();

/*
* Do the template switches...
*/
if ( ($userdata['user_level'] == ADMIN) || ($userdata['user_level'] == MOD) )
{
	foreach ($profile_fields as $field => $post_var)
	{
		$template->assign_block_vars('switch_edit_' . $field, array());
	}
	$template->assign_block_vars('switch_edit_all', array());
}
elseif ($mode == 'editprofile')
{
	$atleast_one_on = false;
	
	foreach ($profile_fields as $field => $post_var)
	{
		if ( ($board_config['as_acp_' . $field] == 'on') || ($board_config['as_acp_' . $field] == 'required') || ($board_config['as_acp_' . $field] == 'reg off') || ( ($board_config['as_acp_' . $field] == 'post count') && ($userdata['user_posts'] >= $board_config['as_acp_' . $field . '_post']) ) ) 
		{
			$template->assign_block_vars('switch_edit_' . $field, array());
			if ($board_config['as_acp_' . $field] == 'required')
			{
				$template->assign_vars(array(
					strtoupper($field) . '_REQUIRED'	=> ' *')
					);
			}
			$atleast_one_on = true;
		}
	}

	if ($atleast_one_on)
	{
		$template->assign_block_vars('switch_edit_all', array());
	}
}
elseif ($mode == 'register')
{
	$atleast_one_on = false;

	foreach ($profile_fields as $field => $post_var)
	{
		if ( ($board_config['as_acp_' . $field] == 'on') || ($board_config['as_acp_' . $field] == 'required') ) 
		{
			$template->assign_block_vars('switch_edit_' . $field, array());
			if ($board_config['as_acp_' . $field] == 'required')
			{
				$template->assign_vars(array(
					strtoupper($field) . '_REQUIRED'	=> ' *')
					);
			}
			$atleast_one_on = true;
		}
	}

	if ($atleast_one_on)
	{
		$template->assign_block_vars('switch_edit_all', array());
	}
}

/*
* check if someone didn't fill in something right
*/
if ( (isset($HTTP_POST_VARS['submit'])) && ($userdata['user_level'] != ADMIN) && ($userdata['user_level'] != MOD) )
{
	$as_triggers = '';

	if ($mode == 'register')
	{
		if ( empty($HTTP_POST_VARS['username']) || empty($HTTP_POST_VARS['new_password']) || empty($HTTP_POST_VARS['password_confirm']) || empty($HTTP_POST_VARS['email']) )
		{
			$fields_empty = true; // this is so we don't get multiple $lang['Fields_empty'] errors
		}
		else
		{
			$fields_empty = false;
		}

		foreach ($profile_fields as $field => $post_var)
		{
			if ( ($post_var != '') && ( ($board_config['as_acp_' . $field] != 'on') && ($board_config['as_acp_' . $field] != 'required') ) )
			{
				$error = TRUE;
				$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . sprintf($lang['Profile_Error'], $lang[$lang_profile_fields[$field]]);
				$as_triggers .= sprintf($lang['Profile_Error_Email'], $lang[$lang_profile_fields[$field]], $post_var) . '%end_of_line%';
			}
			else if ( ($post_var == '') && ($board_config['as_acp_' . $field] == 'required') )
			{
				$error = TRUE;
				if (!$fields_empty)
				{
					$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Fields_empty'];
					$fields_empty = true;
				}
				$as_triggers .= sprintf($lang['Profile_Error_Email_Required'], $lang[$lang_profile_fields[$field]]) . '%end_of_line%';
			}
		}
	}
	else if ($mode == 'editprofile')
	{
		$fields_empty = false;
		foreach ($profile_fields as $field => $post_var)
		{
			if ( ($post_var != '') && ( ($board_config['as_acp_' . $field] == 'off') || ( ($board_config['as_acp_' . $field] == 'post count')  && ($userdata['user_posts'] < $board_config['as_acp_' . $field . '_post']) ) ) )
			{
				$error = TRUE;
				$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . sprintf($lang['Profile_Error'], $lang[$lang_profile_fields[$field]]);
				$as_triggers .= sprintf($lang['Profile_Error_Email'], $lang[$lang_profile_fields[$field]], $post_var) . '%end_of_line%';
			}
			else if ( ($post_var == '') && ($board_config['as_acp_' . $field] == 'required') )
			{
				$error = TRUE;
				if (!$fields_empty)
				{
					$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Fields_empty'];
					$fields_empty = true;
				}
				$as_triggers .= sprintf($lang['Profile_Error_Email_Required'], $lang[$lang_profile_fields[$field]]) . '%end_of_line%';
			}
		}
	}
	
	if (!$board_config['as_acp_log_profile'])
	{
		$as_triggers = '';
	}
}
?>
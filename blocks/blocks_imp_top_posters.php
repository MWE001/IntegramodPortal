<?php
/***************************************************************************
 *                         blocks_imp_top_posters.php
 *                            -------------------
 *   begin                : Sunday, May 02, 2004
 *   copyright            : (C) 2004 masterdavid - Ronald John David
 *   website              : http://www.integramod.com
 *   email                : webmaster@integramod.com
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

if(!function_exists(imp_top_posters_func))
{
	function imp_top_posters_func()
	{
		global $lang, $template, $portal_config, $board_config, $db, $phpEx;

		$sql = "SELECT username, user_id,  user_posts,  user_avatar, user_avatar_type, user_allowavatar
				FROM " . USERS_TABLE . "
				WHERE user_id <> " . ANONYMOUS . "
				ORDER BY user_posts DESC LIMIT ". $portal_config['md_total_poster'];
		if( !($result = $db->sql_query($sql)) )
		{
				message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
		}

		if ( $row = $db->sql_fetchrow($result) )
		{
				$i = 0;
				do
				{
						$username = $row['username'];
						$user_id = $row['user_id'];
						$posts = ( $row['user_posts'] ) ? $row['user_posts'] : 0;
						$poster_avatar = '';
						if ( $row['user_avatar_type'] && $user_id != ANONYMOUS && $row['user_allowavatar'] )
						{
								switch( $row['user_avatar_type'] )
								{
										case USER_AVATAR_UPLOAD:
												$poster_avatar = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $row['user_avatar'] . '" alt="" border="0" height="' . $portal_config['md_avatar_height'] . '" />' : '';
												break;
										case USER_AVATAR_REMOTE:
												$poster_avatar = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $row['user_avatar'] . '" alt="" border="0" height="' . $portal_config['md_avatar_height'] . '" />' : '';
												break;
										case USER_AVATAR_GALLERY:
												$poster_avatar = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $row['user_avatar'] . '" alt="" border="0" height="' . $portal_config['md_avatar_height'] . '" />' : '';
												break;
								}
						}



						$template->assign_block_vars('topposter', array(
								'USERNAME' => $username,
								'POSTS' => $posts,
								'AVATAR_IMG' => $poster_avatar,
								'U_VIEWPOSTER' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id"),
								'U_VIEWPOSTS' => append_sid("search.php?search_author=" . $username . "&showresults=posts")
							)
						);

						$i++;
				}
				while ( $row = $db->sql_fetchrow($result) );
		}

		$template->assign_vars(array(
			'L_POSTS' => $lang['Posts']
			)
		);
	}
}

imp_top_posters_func();
?>
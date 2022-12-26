<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

function exec_ogp_module()
{
    global $db;
    global $view;
	
	$group_id = $_GET['group_id'];
	
	if ( !$db->isAdmin($_SESSION['user_id']) )
	{
		$result = $db->getUserGroupList($_SESSION['user_id']);
		foreach ( $result as $row ) #loop through the groups
		{
			if ( $row['group_id'] == $group_id )
			{
				$own_group = TRUE;
			}
		}
	}
	
	if(!$db->isAdmin($_SESSION['user_id']) && !isset($own_group)) 
	{
		echo "<p class='note'>".get_lang('not_available')."</p>";
		return;
	}
	
    // Delete user from group.
    if( isset($_GET['group_id']) && isset($_GET['user_id']) )
    {
        $group_id = trim($_GET['group_id']);
        $user_id = trim($_GET['user_id']);

        if ( !$db->delUserFromGroup($user_id, $group_id))
        {
            print_failure(get_lang_f('could_not_delete_user_from_group', $user_id, $group_id));
            $view->refresh("?m=user_admin");
            return;
        }

        echo "<p class='success'>".get_lang_f('successfully_removed_from_group', $user_id, $group_id)."</p>";
		$db->logger(get_lang_f('successfully_removed_from_group', $user_id, $group_id));
        $view->refresh("?m=user_admin&amp;p=show_groups");
    }
}
?>

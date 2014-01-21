<?php

/*
	Copyright (C) 2014, Samo Penič, Solidarnost.si

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/




//add_action( 'init', 'solis_add_roles',0 );
/** Function adds custom user roles needed for users that can only vote for proposals or administer (moderate) proposals. */
function solis_add_roles(){
remove_role('proposal_author');
remove_role('proposal_moderator');
add_role(
    'proposal_author',
    __( 'Proposal author', 'solis' ),
    array(
        'read'         => true,  
        'edit_posts'   => false, // cannot post classic posts
        'delete_posts' => false, // cannot delete posts
    )
);
add_role(
    'proposal_moderator',
    __( 'Proposal moderator', 'solis' ),
    array(
        'read'         => true,  // true allows this capability
        'edit_posts'   => false,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

   global $wp_roles;
/* Add capabilities to new roles */
		 $wp_roles->add_cap( 'proposal_author', 'read_proposal' );
   //         $wp_roles->add_cap( 'proposal_author', 'read_private_proposals' );
            $wp_roles->add_cap( 'proposal_author', 'edit_proposal' );
            $wp_roles->add_cap( 'proposal_author', 'edit_published_proposals' );
         //   $wp_roles->add_cap( 'proposal_author', 'edit_others_proposals' );
	 //   $wp_roles->add_cap( 'proposal_author', 'edit_private_proposals' );
            $wp_roles->add_cap( 'proposal_author', 'publish_proposals' );
            $wp_roles->add_cap( 'proposal_author', 'edit_proposals' );
            $wp_roles->add_cap( 'proposal_author', 'delete_proposal' );
            $wp_roles->add_cap( 'proposal_author', 'delete_proposals' );
//            $wp_roles->add_cap( 'proposal_author', 'delete_private_proposals' );
      //      $wp_roles->add_cap( 'proposal_author', 'delete_others_proposals' );
            $wp_roles->add_cap( 'proposal_author', 'delete_published_proposals' );
		$wp_roles->add_cap('proposal_author', 'assign_proposal_topic');
		$wp_roles->add_cap('proposal_author', 'delete_proposal_topic');
		$wp_roles->add_cap('proposal_author', 'assign_proposal_tags');
		$wp_roles->add_cap('proposal_author', 'delete_proposal_tags');


		 $wp_roles->add_cap( 'proposal_moderator', 'read_proposal' );
            $wp_roles->add_cap( 'proposal_moderator', 'read_private_proposals' );
            $wp_roles->add_cap( 'proposal_moderator', 'edit_proposal' );
            $wp_roles->add_cap( 'proposal_moderator', 'edit_published_proposals' );
            $wp_roles->add_cap( 'proposal_moderator', 'edit_others_proposals' );
	    $wp_roles->add_cap( 'proposal_moderator', 'edit_private_proposals' );
            $wp_roles->add_cap( 'proposal_moderator', 'publish_proposals' );
            $wp_roles->add_cap( 'proposal_moderator', 'edit_proposals' );
            $wp_roles->add_cap( 'proposal_moderator', 'delete_proposal' );
            $wp_roles->add_cap( 'proposal_moderator', 'delete_proposals' );
            $wp_roles->add_cap( 'proposal_moderator', 'delete_private_proposals' );
            $wp_roles->add_cap( 'proposal_moderator', 'delete_others_proposals' );
            $wp_roles->add_cap( 'proposal_moderator', 'delete_published_proposals' );

	$wp_roles->add_cap('proposal_moderator', 'assign_proposal_topic');
	$wp_roles->add_cap('proposal_moderator', 'delete_proposal_topic');
	$wp_roles->add_cap('proposal_moderator', 'assign_proposal_tags');
	$wp_roles->add_cap('proposal_moderator', 'delete_proposal_tags');



/* Add new roles to existing wp roles! */

        if ( isset($wp_roles) ) {
		/* admin ca do all things */
            $wp_roles->add_cap( 'administrator', 'read_proposal' );
            $wp_roles->add_cap( 'administrator', 'read_private_proposals' );
            $wp_roles->add_cap( 'administrator', 'edit_proposal' );
            $wp_roles->add_cap( 'administrator', 'edit_published_proposals' );
            $wp_roles->add_cap( 'administrator', 'edit_others_proposals' );
	    $wp_roles->add_cap( 'administrator', 'edit_private_proposals' );
            $wp_roles->add_cap( 'administrator', 'publish_proposals' );
            $wp_roles->add_cap( 'administrator', 'edit_proposals' );
            $wp_roles->add_cap( 'administrator', 'delete_proposal' );
            $wp_roles->add_cap( 'administrator', 'delete_proposals' );
            $wp_roles->add_cap( 'administrator', 'delete_private_proposals' );
            $wp_roles->add_cap( 'administrator', 'delete_others_proposals' );
            $wp_roles->add_cap( 'administrator', 'delete_published_proposals' );

	$wp_roles->add_cap('administrator', 'manage_proposal_topic');
	$wp_roles->add_cap('administrator', 'edit_proposal_topic');
	$wp_roles->add_cap('administrator', 'assign_proposal_topic');
	$wp_roles->add_cap('administrator', 'delete_proposal_topic');
	$wp_roles->add_cap('administrator', 'manage_proposal_tags');
	$wp_roles->add_cap('administrator', 'edit_proposal_tags');
	$wp_roles->add_cap('administrator', 'assign_proposal_tags');
	$wp_roles->add_cap('administrator', 'delete_proposal_tags');

		/* editor can do many things */
		 $wp_roles->add_cap( 'editor', 'read_proposal' );
            $wp_roles->add_cap( 'editor', 'read_private_proposals' );
            $wp_roles->add_cap( 'editor', 'edit_proposal' );
            $wp_roles->add_cap( 'editor', 'edit_published_proposals' );
            $wp_roles->add_cap( 'editor', 'edit_others_proposals' );
	    $wp_roles->add_cap( 'editor', 'edit_private_proposals' );
            $wp_roles->add_cap( 'editor', 'publish_proposals' );
            $wp_roles->add_cap( 'editor', 'edit_proposals' );
            $wp_roles->add_cap( 'editor', 'delete_proposal' );
            $wp_roles->add_cap( 'editor', 'delete_proposals' );
            $wp_roles->add_cap( 'editor', 'delete_private_proposals' );
            $wp_roles->add_cap( 'editor', 'delete_others_proposals' );
            $wp_roles->add_cap( 'editor', 'delete_published_proposals' );

	$wp_roles->add_cap('editor', 'assign_proposal_topic');
	$wp_roles->add_cap('editor', 'delete_proposal_topic');
	$wp_roles->add_cap('editor', 'assign_proposal_tags');
	$wp_roles->add_cap('editor', 'delete_proposal_tags');

		/* author can do some things */
		 $wp_roles->add_cap( 'author', 'read_proposal' );
   //         $wp_roles->add_cap( 'author', 'read_private_proposals' );
            $wp_roles->add_cap( 'author', 'edit_proposal' );
            $wp_roles->add_cap( 'author', 'edit_published_proposals' );
         //   $wp_roles->add_cap( 'author', 'edit_others_proposals' );
	 //   $wp_roles->add_cap( 'author', 'edit_private_proposals' );
            $wp_roles->add_cap( 'author', 'publish_proposals' );
            $wp_roles->add_cap( 'author', 'edit_proposals' );
            $wp_roles->add_cap( 'author', 'delete_proposal' );
            $wp_roles->add_cap( 'author', 'delete_proposals' );
//            $wp_roles->add_cap( 'author', 'delete_private_proposals' );
      //      $wp_roles->add_cap( 'author', 'delete_others_proposals' );
            $wp_roles->add_cap( 'author', 'delete_published_proposals' );
		$wp_roles->add_cap('author', 'assign_proposal_topic');
		$wp_roles->add_cap('author', 'delete_proposal_topic');
		$wp_roles->add_cap('author', 'assign_proposal_tags');
		$wp_roles->add_cap('author', 'delete_proposal_tags');
       }


}


?>

Pilau Favourites
==========

A WordPress plugin for basic management of favourite posts and pages.

## Installation

Note that the plugin folder should be named `favourites`. This is because if the [GitHub Updater plugin](https://github.com/afragen/github-updater) is used to update this plugin, if the folder is named something other than this, it will get deleted, and the updated plugin folder with a different name will cause the plugin to be silently deactivated.

## Basic use

In the setup for your theme:

	global $PF;
	$PF = null;
	if ( class_exists( 'Pilau_Favourites' ) ) {
		$PF = Pilau_Favourites::get_instance();
	}

In the template where you want a favourite link:

	global $PF;
	echo $PF->favourite_link();

Is the current post favourited by the current user?

	if ( $PF->favourited() ) {
		// Do stuff
	}

Get the current user's favourites (an array of post IDs):

	$current_user_favourites = $PF->get_user_favourites();

## Filter hooks

* `pf_add_favourite_text` - Link text for adding a favourite
* `pf_remove_favourite_text` - Link text for adding a favourite

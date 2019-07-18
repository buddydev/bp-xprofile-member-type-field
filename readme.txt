=== BuddyPress Xprofile Member Type Field ===
Contributors: buddydev, sbrajesh
Tags: buddypress
Requires at least: 4.5+
Tested up to: 5.2.2
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BuddyPress Xprofile Member Type Field plugin allows site admins to use BuddyPress member type as profile field.

== Description ==

BuddyPress Xprofile Member Type Field plugin allows site admins to use BuddyPress member type as profile field. It lest user choose their member type and the updates user’s member type based on their selection of the member type in the profile field.

Features:-

* Uses Member Type As Xprofile Field
* Updates & syncs user’s member type with the xprofile field and vice versa
* Works with [Non Editable Profile field](https://buddydev.com/plugins/bp-non-editable-profile-fields/) plugin if you don’t want to allow users to modify their member type after registration.
* Searchable Member type fields(added in 1.0.1) when using [BP Profile Search](https://wordpress.org/plugins/bp-profile-search/) plugin.

The current version shows the registered member types as a select element(dropdown).

**For Support**, Please use [BuddyDev Support Forums](https://buddydev.com/support/forums/ ).

== Installation ==

1. Visit Dashboard->Plugins->add New
2. Search for BuddyPress Xprofile Member Type Field
3. Install this plugin
4. Click Activate
5. Now, you can see new option in edit field page. "Single Member Type" in the field type dropdown.

For more details, Please see the plugin documentation page.

== Screenshots ==

1. Edit field screen screenshot-1.png
2. Registration page  screenshot-2.png
3. Edit profile page screenshot-3.png

== Changelog ==
= 1.0.8 =
* Add filter to allow selecting default member types on registration.

== Changelog ==
= 1.0.7 =
* Fix field description. Also, use the new field html as used by core fields.

= 1.0.6 =
* Added compatibility with BP Profile search 4.8+

= 1.0.5 =
* Fixed recursion on selecting empty field.
* Allow filtering allowed member types in the field using 'bp_xprofile_member_type_field_allowed_types' filter.
* Fix a bug where member type was not syncing to the profile field if the use's profile field was not set earlier.

= 1.0.4 =
* Fix the infinite loop on member deletion.

= 1.0.3 =
* Updated for 2 way synchronization of the membertype and profile fields.

= 1.0.2 =
* Updated for allowing to use radio field using a filter

= 1.0.1 =
* Added compatibility with BP Profile Search plugin for searchable member type field

= 1.0.0 =
* initial release

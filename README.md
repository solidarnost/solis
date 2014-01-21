solis
=====

Wordpress plugin for participative democracy
--------------------------------------------

Plugin that helps with decision making by making proposals, allowing users to comment on them by providing arguments why they do agree with proposal or they don't. There is not much room for debate (still, the post can be linked to forum of some kind if this functionality is required).

The file provides plugin and corresponding theme. Some functionality is not guaranteed with any theme. Theme provided is derived (child) theme from twentyfourteen.

Many thanks to people from danesjenovdan.si, who helped us with ideas. Our implementation is based on their design, but code is totally rewrited.


Requirement:
------------

A single one:

* Wordpress 3.8 with twentyfourteen theme

Installation:
-------------

1. Make sure you have a working WP installation
2. copy contents of wp-content into your WP installation base directory.
3. Go to administration control panel.
4. From Menu->Plugins->Installed: Activate plugin 'Solis'.
5. From Menu->Apperance->Themes: Activate theme 'Solis'.
6. Add some discussion fields by providing new Proposal topics. Go to Menu->Proposal->Topic and add some topics.
7. Edit menus in Menu->Apperance->Menus. I suggest to add Proposal topics into secondary menu. Keep primary for logout, etc. DO MAKE primary menu. It can be empty, but it will be automatically populated with logout button.
8. Make a page, that allows users to change password. In page put shortcode `[password_form]`. Put it into primary menu.
9. Assign users a role of "Post Author". They will be able to publish Proposal. Also classic "Author" can publish proposals. Use "Post moderator" or classic "Editor" to moderate posts.
10. Use Menu->Users->Add basic CSV with caution. It adds new users based on CSV (first name, last name, email) and notifies them of the newly created password. It gives them WP site default role.
11. Protect your page if neccessary, so that users, that can't login can't see proposals! Use plugins from WP repositories.


Provides:
---------

* custom post type Proposal with all the custom capabilities
* custom post taxonomies: Proposal Topic, Proposal Tag
* two additional roles: Proposal author, Proposal moderator
* shortcode for WP password change `[password_form]`
* Theme for voting and displaying the results. Theme also shows arguments in two columns (agree/disagree type).
* Vote can be changed but only once every 5 minutes.
* Users get info on last posts since last Proposal topic archive visited.
* Slovenian translation in PO file.
* lots of good will and collaborative spirit.


Translating:
------------

The plugin uses classic xgettext for translation. Use `poeditor`, and make your custom po file based `solis_plugin.pot` template file from `plugin/solis/languages` folder. Translate from english and save `po` and `mo` file. Add resulting `po` and `mo` file back into `plugin/solis/languages` folder. And don't forget to share your translations. We will gladly add it to our git repository.

Develop:
--------

Feel free to fork the project, develop your own features, send us feature requests and bug reports. Send us your modifications. Share software under GPLv3 licence.


Document:
---------

As usual, programers are too lazy to write documentation. Please help by documenting the source code and writting documentation for the system.



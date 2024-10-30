=== HTTPS Updates ===
Contributors: WhiteFirDesign
Donate link: https://supporters.eff.org/donate
Tags: updates, security, https, plugins, themes
Requires at least: 3.3.1
Tested up to: 3.6
Stable tag: trunk

Perform WordPress, plugin, and theme update checks and download updated versions over HTTPS.

== Description ==

**The plugin's functionality is built-in to WordPress 3.7+.**

WordPress performs update checks without verifying the information came from wordpress.org, which leaves the update information vulnerable to being modified by a man-in-the-middle attack. Update downloads are also insecure because the downloaded file is not checked to verify that it has not been modified from the version on wordpress.org. This plugin reduces the chance of a successful man-in-the-middle attack by modifying the update process so that update checks and update downloads are performed using a HTTPS connection to wordpress.org. This process is still vulnerable to improper HTTPS handling on the server hosting the WordPress installation, to the attacker gaining access to a SSL certificate for wordpress.org, or a weakness in the underlying SSL encryption.

The plugin also modifies the plugin and theme installation processes to make them use a HTTPS connection.

The plugin requires that a proper HTTPS connection can be made on the server hosting the WordPress installation and that a HTTPS connection can be made to wordpress.org. A diagnostic tool is included with the plugin so that you can check if those things are available.

The plugin does not secure the update process of plugins and themes that are not updated through wordpress.org.

If there are pending updates available at the time the plugin in installed those will not be downloaded over HTTPS until after the next update check occurs and the relevant download links are modified so that the download is done using a HTTPS connection.

**Supported Localizations:** Deutsch, Español, Français

Please let us know if you are interested in us adding additional localizations.

== Installation ==

1. Copy plugin files to the plugins folder.

2. Activate the plugin.

3. Check Diagnostic Tool to insure you can connect to wordpress.org with HTTPS.

== Screenshots ==

1. Diagnostic Tool

== Changelog ==

= 1.0.2 =
* Added French, German, and Spanish localizations

= 1.0.1 =
* Fixed issue that caused partial core upgrade not to be downloaded over HTTPS

= 1.0 =
* Initial release
=== CopySafe PDF Protection ===

Contributors: ArtistScope
Donate link: https://artistscope.com/copysafe_pdf_protection_wordpress_plugin.asp
Tags: protect pdf, secure pdf, prevent copy, pdf protection, copy protect
Tested up to: 6.6
Requires at least: 5.0
Stable tag: 1.31
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display copy protected PDF documents in WordPress pages and posts.

== Description ==

Copy protect PDF. No other WordPress plugin can securley protect PDF from all copy because they are JavaScript and HTML only. The difference with CopySafe plugins is that they provide the means to use more robust solutions that can interact at system level which is where copy needs to be actioned.

This plugin enables the use of copy protected PDF documents in WordPress posts and pages. The resulting embedded PDF object is supported in the ArtisBrowser and others that still support NPAPI plugins. 

CopySafe PDF provides the most secure protection for PDF both online and offline. This plugin displays PDF created for use online and when domain lock is applied, the PDF file cannot be displayed from anywhere else. With PDF now bound to your web site, you are free to apply DRM rules to control access to the page via your CMS member controls. The ArtisBrowser reports the user's unique computer signature so that you can convert WordPress into a fully fledged DRM Portal by adding a new field to your member's table and some script to compare current ID with the one already assigned.

* Easy install (requires WP Classic Editor).
* Insert protected PDF into posts and pages using the [PDF] media button.
* Upload and embed secure PDF using WordPress native media tools.
* Embeds objects dynamically using JavaScript.
* Set varying levels of protection per page or post.
* Detects PDF plugin version for redirection and installation.
* Settings to control width and height of the browser reader area.
* Safe from Print Screen and all screen capture software.
* PDF documents cannot be saved and displayed away from your website.

For more information visit the [CopySafe PDF](https://artistscope.com/copysafe_pdf_protection_wordpress_plugin.asp) website.


== Installation ==

This section describes how to install the plugin and get it working.

1. Install the WordPress Classic Editor plugin.
2. Upload the "wp-copysafe-pdf" folder and its contents to the "/wp-content/plugins/" directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Create a new folder at "/wp-content/uploads/copysafe-pdf".
5. Set write permissions on this new folder for "everyone.
6. Check and modify the default settings to suit your pages.
7. You can now add CopySafe PDF documents using its media button above the post editor.

Click to download the [CopySafe PDF Install Guide](https://artistscope.com/docs/CopySafePDF_WordPress_Installation.pdf)
 
If you do not have any CopySafe PDF encrypted documents for testing, you can create your own by downloading the demo version of [CopySafe PDF Protector]( https://artistscope.com/copysafe_pdf_protection.asp) software or by downloading use this [test bundle](https://artistscope.com/download/CopysafePDF_testfiles.zip) which includes several sample documents. The samples are not domain locked so they can be displayed on any site.

If you have problems installing, email us from the www.artistscope.com web site for a faster response than the WP forum.

== Implementation ==

Click on the [PDF] media button above a post to upload and embed CopySafe PDF documents in your current post or page. When inserting a protected PDF object, the necessary shortcode is automatically inserted into the post editor. 

You can upload new PDF class documents or select from a list of already uploaded documents. After selecting a document you can then set the security options to apply to the page such as:

* Disable protection from PrintScreen and screen capture.
* Disable the option to view from computers using remote or virtual connections.
* Disable printing or set a print limit per session.**

** To extend print count beyond the current session, you need to implement some DRM rules and monitor prints per user/document.

== Screenshots ==

1. CopySafe PDF documents can be added at the last position of the cursor by clicking the [PDF] button.
2. Once the document is selected, you can then nominate settings to apply to this particular page or post.
3. The above default settings are applied to all CopySafe PDF pages and posts.
4. The document file manager will list all uploaded document class files.

== Changelog ==

= 1.31 =
* Security fix.
* Tested for compatibility with WP 6.6.

= 1.30 =
* Security fix.

= 1.29 =
* Fix for VSS vulnerability.

= 1.28 =
* Tested for compatibility with WP 6.2.

= 1.26 =
* Tested for compatibility with WP 6.0.

= 1.25 =
* Tested for compatibility with WP 5.9.

= 1.24 =
* Tested for compatibility with WP 5.7.

= 1.23 =
* Tested for compatibility with WP 5.6.
* Revised for JS changes.

= 1.22 =
* Tested for compatibility with WP 5.4.
* Revised download page for new ArtisBrowser versions.

= 1.21 =
* Removed upload file size limitation.
* Revised minimum version detection for ArtisBrowser.

= 1.20 =
* Tested for compatibility with WP 5.3.
* Added minimum version detection for ArtisBrowser.

= 1.19 =
* WP security upgrade.

= 1.18 =
* Verified for WP version 5.0.
* Requires WP Classic EDitor.

= 1.17 =
* Bugfix for when minimum file size is not specified.

= 1.16 =
* Bugfix for file uploads on WP 4.9.8.

= 1.15 =
* Revised download support pages.

= 1.14 =
* Bugfix for WordPress 4.9.1

= 1.13 =
* Corrected SSL option for remote CSS support.
* Tested for WordPress 4.9.

= 1.12 =
* Tested for WordPress 4.8.

= 1.11 =
* Revised security for upload process.

= 1.10 =
* Revised nonce.

= 1.9 =
* Tested for WordPress 4.7.
* Revised upload function to suit changes in WP 4.7.

= 1.8 =
* Tested for WordPress 4.6.
* Updated download and support links.

= 1.7 =
* Uses WP upload functions instead of Uploadify.

= 1.6 =
* Tested for WordPress 4.3.

= 1.5 =
* Detection update for Windows 10.

= 1.4 =
* Added compatibilty for ASPS web browser.
* Added ASPS as a browser requirement in settings.
* Updated download pages to cater for lack of IE and Chrome plugin support.

= 1.3 =
* Tested for compatibility with WP 4.2.

= 1.2 =
* Tested for compatibility with WP 4.1.

= 1.1 =
* Bugfix for user session checking

= 1.0 =
* Tested and verified on WordPress version 4.0

= 0.9 =
* Tested and verified on WordPress version 3.9.2
* Added alternative user check in case session logging not supported by webhost.
* Added settings option to allow uploads by admin only.
* Upload will progress only on same host IP.
* Referrer user agent must be Shockwave Flash
* Referrer url must match with the same script name.
* Save settings page options altered for show in smaller screens.
* No need to click "Insert File to editor" button after Save button clicked.

= 0.8 =
* Modified logged-in user requirement for upload function.

= 0.7 =
* Fixed security flaw in upload function.
* Tested and aproved for WordPress 3.9.

= 0.6 =
* Changed the download link to point to an ArtistScope server.

= 0.5 =
* Added new languages for a new total of 27 langauages.

= 0.4 =
* Fixed IE-11 detection bug.

= 0.3 =
* Supported in Wordpress version 3.7
* Support for Windows 8.1

= 0.2 =
* Added allowance for Firefox 20 and later

= 0.1 =
* First release.

== About ==

* The CopySafe PDF Reader is a free download and can be distributed by email, download or on disk with your protected PDF documents.
* The CopySafe PDF Reader installer includes a secure reader and a browser plugin.
* The CopySafe PDF Reader is supported across all Windows computers since XP.
* Protected PDF documents for distribution by email, download and on disk are in .ENC format.
* Protected PDF documents for online viewing are in .CLASS format.
* The CopySafe PDF Protector software is required to encrypt and convert PDF for .ENC or .CLASS formats.
* Documents converted by an unlicensed (demo) version the Protector will have a watermark applied.
* Documents created by the "demo" version cannot be protected by DRM or Domain Lock

== Licensing  ==

* CopySafe PDF is the most secure document protection solution on the planet
* Licensing the software removes all limitations and watermarks
* Licensing also enables use of DRM to protect unauthorized distribution and sharing
* Licensing enables the use of "Domain Lock" on .CLASS documents
* Use of the DRM validation service provided by the ArtistScope DRM Portal for free

== Other versions ==

CopySafe PDF is a sophisticated application specially designed for a wide variety of scenarios.

* The CopySafe PDF Protector is available as Windows desktop software.
* The CopySafe PDF Protector can be operated by Command-line on Windows computers and servers.
* Custom DLLs are available to interface the CMD version with web page scripts.
* DRM Portal software is available for installation on your Windows server.
* Free DRM Hosting is provided with every CopySafe PDF license.

For evaluation of DRM validation, create a demo account at the [CopySafe Demo](https://artistscope.net/drm/) website.

==Limitations==

* CopySafe PDF is supported on all Windows computers since XP by using the ArtisBrowser. Mac users can run Windows via Bootcamp or install Parallels to run Windows. If support for all devices is required, the most secure solution for all devices is the ArtistScope Site Protection Software at https://artistscope.com


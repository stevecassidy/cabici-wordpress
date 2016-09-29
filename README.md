Cabici Races Wordpress Plugin
===========================

The cabici.net plugin integrates race listings from cabici.net into a Wordpress
site. The plugin provides a number of content options.

The options page for the plugin allows you to select the default club that will be
used in finding race listings and results.

Installation
============

Download the file [cabici.zip](https://bitbucket.org/stevecassidy/wordpress-cabici/downloads/cabici.zip)
and install into your Wordpress instance via the dashboard.

Shortcodes
==========

Two shortcodes are provided for including content into pages generated from
cabici.net.  

   [cabici_race_schedule]

This shortcode inserts the full schedule of races from the configured club into the
page. All future races stored in Cabici for the configured club will be listed in
date order, most recent first.

   [cabici_last_result]

This shortcode will insert a set of tables containing the results of the most
recent race entered into Cabici.  The race selected will the the most recent
one that has had results uploaded.  

Widgets
=======

The plugin implements three widgets to insert content from Cabici into your
Wordpress site.

Cabici Next Race Widget
-----------------------

This widget is intended for a side-bar and displays the details of the next
scheduled race for the configured club.  

Cabici Result Widget
--------------------

This widget is intended for in-page (it uses a wider display) and displays a
compact version of the results from the most recent race.  


Cabici Race List Widget
-----------------------

This widget is intended for a side-bar and displays the next 5 races from
all clubs listing races on Cabici.  

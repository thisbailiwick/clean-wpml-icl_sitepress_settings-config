# clean-wpml-icl_sitepress_settings-config
Clean out old and unused settings in WPML's `icl_sitepress_settings` row in the wp_options table.

## The Issue
Depending on how many pages you have, if you use ACF, and if your pages are ordered differently in different languages, you may have a lot of data in the `icl_sitepress_settings` row in the `wp_options` table. On WPEngine any `wp_options` table row which is set to autoload and larger than 1mb will be deleted by a WPEngine cron job that runs overnight.

## The Solution
This is possibly just a partial solution as your config can continue to grow. I've seen in the site I was working in at the time that there were many old an unused values do to ACF structure changing and fields being removed.
This script will get the value of the `wp_options` row where the `meta_key` is `icl_sitepress_settings`. The `custom_term_fields_translation` and `custom_fields_translation` elements from the value of that row will be compared to the `wp_postmeta` table to see if the `icl_sitepress_settings` indices have a matching row in the `wp_postmeta` table where the `meta_key` is a match. If there is no match then the indice is removed from the `icl_sitepress_settings`. This will not remove any data from the `wp_postmeta` table.

## Warning
Make a backup of your existing `icl_sitepress_settings` row in the `wp_options` table before running the script. And your whole db for that matter.
I have seen a fatal error happen when running the script while the WPML status bar was showing that it was processing items. I'm not sure if this was a coincidence or not but I would recommend running this script when you see that the status bar is not showing that it is processing items.

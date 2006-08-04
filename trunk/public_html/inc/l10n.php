<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE

include_once('inc/streams.php');
include_once('inc/gettext.php');


function get_locale() {
	global $locale, $config;

	if (isset($locale))
		return $locale;

	// $config['lang'] is defined in config.php
	if ($config['lang']) {
    $locale = $config['lang'];
	}
	
	if (empty($locale)) {
    $locale = 'es_ES';
	}

	return $locale;
}

// Return a translated string.    
function __($text, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain])) {
		return $l10n[$domain]->translate($text);
	} else {
		return $text;
	}
}

// Echo a translated string.
function _e($text, $domain = 'default') {
	global $l10n;
	
	if (isset($l10n[$domain])) {
		echo $l10n[$domain]->translate($text);
	} else {
		echo $text;
	}
}

// Return the plural form.
function __ngettext($single, $plural, $number, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain])) {
		return $l10n[$domain]->ngettext($single, $plural, $number);
	} else {
		if ($number != 1)
			return $plural;
		else
			return $single;
	}
}

function load_textdomain($domain, $mofile) {
	global $l10n;

	if (isset($l10n[$domain])) {
		return;
	}

	if ( is_readable($mofile)) {
    $input = new CachedFileReader($mofile);
	}	else {
		return;
	}

	$l10n[$domain] = new gettext_reader($input);
}

function load_default_textdomain() {
	global $l10n;

	$locale = get_locale();
	$mofile = "inc/languages/$locale.mo";
	
	load_textdomain('default', $mofile);
}


?>
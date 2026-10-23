<?php
/**
 * Master Theme Bootstrapper / Kernel
 *
 * @package Jubelio
 */

namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Jubelio\Integrations\ContactForm7;
use Jubelio\Integrations\Tracking;
use Jubelio\Integrations\Bitrix24;
use Jubelio\Integrations\FormLeadHandler;
use Jubelio\Integrations\ElementorQueries;
use Jubelio\Integrations\ElementorManager;
use Jubelio\Shortcodes\LegacyShortcodes;
use Jubelio\Modules\CeritaPebisnis\Module as CeritaPebisnisModule;
use Jubelio\Modules\Ebook\PostType as EbookPostType;

class Theme {

	/**
	 * Boot all core services and feature modules
	 */
	public static function init() {
		// 1. Core Services & Performance
		AcfSync::init();
		Enqueue::init();
		AdminCleanup::init();
		Performance::init();

		// 2. Integrations
		ContactForm7::init();
		Tracking::init();
		Bitrix24::init();
		FormLeadHandler::init();
		ElementorQueries::init();
		ElementorManager::init();

		// 3. Shortcodes
		LegacyShortcodes::init();

		// 4. Feature Modules (Vertical Slices)
		CeritaPebisnisModule::init();
		EbookPostType::init();
	}
}

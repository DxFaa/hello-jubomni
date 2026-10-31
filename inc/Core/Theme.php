<?php
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

	public static function init() {
		AcfSync::init();
		Enqueue::init();
		AdminCleanup::init();
		Performance::init();

		ContactForm7::init();
		Tracking::init();
		Bitrix24::init();
		FormLeadHandler::init();
		ElementorQueries::init();
		ElementorManager::init();

		LegacyShortcodes::init();

		CeritaPebisnisModule::init();
		EbookPostType::init();
	}
}

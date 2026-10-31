<?php
namespace Jubelio\Modules\CeritaPebisnis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Jubelio\Integrations\ElementorManager;
use Jubelio\Modules\CeritaPebisnis\Widgets\GridWidget;
use Jubelio\Modules\CeritaPebisnis\Widgets\MetricsWidget;
use Jubelio\Modules\CeritaPebisnis\Widgets\TestimonialsWidget;
use Jubelio\Modules\CeritaPebisnis\Widgets\SingleHeroWidget;
use Jubelio\Modules\CeritaPebisnis\Widgets\SingleClientOverviewWidget;
use Jubelio\Modules\CeritaPebisnis\Widgets\SingleQuoteWidget;
use Jubelio\Modules\CeritaPebisnis\Widgets\SingleResultMetricsWidget;

class Module {

	public static function init() {
		PostType::init();
		Seeder::init();

		ElementorManager::add_widget( GridWidget::class );
		ElementorManager::add_widget( MetricsWidget::class );
		ElementorManager::add_widget( TestimonialsWidget::class );
		ElementorManager::add_widget( SingleHeroWidget::class );
		ElementorManager::add_widget( SingleClientOverviewWidget::class );
		ElementorManager::add_widget( SingleQuoteWidget::class );
		ElementorManager::add_widget( SingleResultMetricsWidget::class );
	}
}

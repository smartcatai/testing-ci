<?php
/**
 * Smartcat Translation Manager for WordPress
 *
 * @package Smartcat Translation Manager for WordPress
 * @author Smartcat <support@smartcat.ai>
 * @copyright (c) 2019 Smartcat. All Rights Reserved.
 * @license GNU General Public License version 3 or later; see LICENSE.txt
 * @link http://smartcat.ai
 */

namespace SmartCAT\WP\Admin;

use Psr\Container\ContainerInterface;
use SmartCAT\WP\Connector;
use SmartCAT\WP\DB\Repository\StatisticRepository;
use SmartCAT\WP\WP\HookInterface;
use SmartCAT\WP\WP\Options;

/**
 * Class StatisticsAjax
 *
 * @package SmartCAT\WP\Admin
 */
final class StatisticsAjax implements HookInterface {
	/**
	 * @var
	 */
	private $prefix;

	/**
	 * StatisticsAjax constructor.
	 *
	 * @param $prefix
	 */
	public function __construct( $prefix ) {
		$this->prefix = $prefix;
	}

	/**
	 * Ручной запуск обновления статистики
	 */
	static public function start_refresh_statistic() {
		$ajax_response = new AjaxResponse();
		if ( ! current_user_can( 'publish_posts' ) ) {
			$ajax_response->send_error( __( 'Access denied', 'translation-connectors' ), [], 403 );
		}

		/** @var ContainerInterface $container */
		$container = Connector::get_container();

		/** @var Options $options */
		$options = $container->get( 'core.options' );
		$queue   = null;

		if ( ! $options->get( 'statistic_queue_active' ) ) {

		}

		$ajax_response->send_success( 'ok' );
	}

	/**
	 *
	 */
	public static function check_refresh_statistic_status() {
		$ajax_response = new AjaxResponse();
		if ( ! current_user_can( 'publish_posts' ) ) {
			$ajax_response->send_error( __( 'Access denied', 'translation-connectors' ), [], 403 );
			wp_die();
		}

		/** @var ContainerInterface $container */
		$container = Connector::get_container();

		/** @var Options $options */
		$options = $container->get( 'core.options' );
		$ajax_response->send_success(
			'ok',
			[ 'statistic_queue_active' => boolval( $options->get( 'statistic_queue_active' ) ) ]
		);
	}

	/**
	 * @return mixed|void
	 */
	public function register_hooks() {
		if ( wp_doing_ajax() ) {
			add_action( "wp_ajax_{$this->prefix}start_statistic", [ self::class, 'start_refresh_statistic' ] );
			add_action( "wp_ajax_{$this->prefix}check_statistic", [ self::class, 'check_refresh_statistic_status' ] );
		}
	}
}

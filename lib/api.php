<?php
/**
 * Project: MinervaKB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MinervaKB {
	/**
	 * Renders search
	 * @param $config
	 */
	public static function put_search($config) {
		MKB_TemplateHelper::render_search(self::parse_search_params($config));
	}

	/**
	 * Renders topics
	 * @param $config
	 */
	public static function put_topics($config) {
		MKB_TemplateHelper::render_topics(self::parse_topics_params($config));
	}

	/**
	 * Maps API search config params to DB settings
	 * @param $params
	 *
	 * @return array
	 */
	private static function parse_search_params($params) {
		return self::map_params(self::get_search_args_map(), $params);
	}

	/**
	 * Maps API topics config params to DB settings
	 * @param $params
	 *
	 * @return array
	 */
	private static function parse_topics_params($params) {
		return self::map_params(self::get_topics_args_map(), $params);
	}

	/**
	 * Gets search args map
	 * @return array
	 */
	private static function get_search_args_map() {
		return array(
			"search_title" => 'title',
			"search_placeholder" => 'placeholder',
			"show_search_icon" => 'show_search_icon',
			"search_icon" => 'search_icon',
			"search_clear_icon" => 'clear_icon',
			"search_clear_icon_tooltip" => 'clear_icon_tooltip',
			"search_tip" => 'tip',
			"disable_autofocus" => 'no_focus',
			"show_topic_in_results" => 'show_topic',
			"search_result_topic_label" => 'topic_label'
		);
	}

	/**
	 * Gets topics args map
	 * @return array
	 */
	private static function get_topics_args_map() {
		return array(
			"topics_title" => "title",
			"home_topics" => "topics",
			"home_view" => "view",
			"home_layout" => "columns",
			"show_articles_count" => "show_count",
			"show_all_switch" => "show_all",
			"show_all_label" => "show_all_label",
			"home_topics_articles_limit" => "limit",
			"articles_count_bg" => "count_bg",
			"articles_count_color" => "count_color",
			"show_topic_icons" => "show_topic_icons",
			"show_article_icons" => "show_article_icons",
			"article_icon" => "article_icon",
			"topic_color" => "topic_color",
			"topic_icon" => "topic_icon"
		);
	}

	/**
	 * Maps params to args map
	 * @param $args_map
	 * @param $args
	 *
	 * @return array
	 */
	private static function map_params($args_map, $args) {
		$settings = array();

		foreach($args_map as $key => $value) {
			if (isset($args[$value])) {
				$settings[$key] = $args[$value];
			}
		}

		return $settings;
	}
}
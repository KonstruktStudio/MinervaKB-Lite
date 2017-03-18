<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

?><section class="mkb-no-results"><?php

	do_action('minerva_no_content_title_before');

	?><header class="mkb-page-header"><?php

		do_action('minerva_no_content_title_inside_before');

		?><h1 class="mkb-page-title"><?php esc_html_e(MKB_Options::option( 'search_no_results_title' )); ?></h1><?php

		if (is_search()): // search page

			?><div class="mkb-page-description"><p><?php
				esc_html_e(MKB_Options::option( 'search_no_results_subtitle' )); ?></p></div><?php

		else: // no content for archives/tags

			?><div class="mkb-page-description"><p><?php
				esc_html_e(MKB_Options::option( 'topic_no_results_subtitle' )); ?></p></div><?php

		endif;

		do_action('minerva_no_content_title_inside_after');

	?></header><!-- .mkb-page-header --><?php

	do_action('minerva_no_content_title_after');

	?><div class="mkb-page-content"><?php

		do_action('minerva_no_content_inside');

	?></div><!-- .mkb-page-content -->
</section><!-- .mkb-no-results -->
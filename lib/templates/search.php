<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

get_header();

?><div class="<?php esc_attr_e(MKB_TemplateHelper::root_class('search')); ?>"><?php

	MKB_TemplateHelper::maybe_render_left_sidebar( 'search' );

	?><div class="<?php esc_attr_e(MKB_TemplateHelper::content_class('search')); ?>"><?php

		if (have_posts()): // search returned results

			do_action('minerva_search_title_before');

			?><header class="mkb-page-header"><?php

				do_action('minerva_search_title_inside_before');

				?><h1 class="mkb-page-title"><?php

				global $wp_query;

			    printf( MKB_Options::option('search_results_page_title'),
					 esc_html($wp_query->found_posts),
					 '<span>' . esc_html( get_search_query() ) . '</span>' );
			    ?></h1><?php

				do_action('minerva_search_title_inside_after');

			?></header><?php

			do_action('minerva_search_title_after');

			do_action('minerva_search_loop_before');

			// main search loop
			while ( have_posts() ) : the_post();
				include( MINERVA_KB_PLUGIN_DIR . 'lib/templates/content.php' );
			endwhile;

			do_action('minerva_search_loop_after');

		else: // search returned no results

			include( MINERVA_KB_PLUGIN_DIR . 'lib/templates/no-content.php' );

		endif;
		?></div><!--.mkb-content-main--><?php

	MKB_TemplateHelper::maybe_render_right_sidebar( 'search' );

	?></div><!--.mkb-container--><?php

get_footer();

?>
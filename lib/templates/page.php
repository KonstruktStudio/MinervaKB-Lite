<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

get_header();

?><div class="<?php esc_attr_e(MKB_TemplateHelper::root_class('page')); ?>"><?php

	MKB_TemplateHelper::maybe_render_left_sidebar( 'page' );

	?><div class="<?php echo esc_attr(MKB_TemplateHelper::content_class('page')); ?>"><?php

		while (have_posts()) : the_post(); // main loop

			do_action('minerva_page_title_before');

			?><header class="mkb-page-header"><?php

				do_action('minerva_page_title_inside_before');

				the_title( '<h1 class="mkb-page-title">', '</h1>' );

				do_action('minerva_page_title_inside_after');

			?></header><?php

			do_action('minerva_page_title_after');

			do_action('minerva_page_loop_before');

			?><div class="mkb-page-content"><?php

				do_action('minerva_page_content_inside_before');

				MKB_TemplateHelper::home_content();

				do_action('minerva_page_content_inside_after');

			?></div><!-- .mkb-entry-content --></div><?php

			do_action('minerva_page_loop_after');

		endwhile;

	MKB_TemplateHelper::maybe_render_right_sidebar( 'page' );

	?></div><!--.mkb-container--><?php

get_footer();

?>
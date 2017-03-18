<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

get_header();

?><div class="<?php esc_attr_e(MKB_TemplateHelper::root_class('article')); ?>"><?php

		MKB_TemplateHelper::maybe_render_left_sidebar( 'article' );

		?><div class="<?php esc_attr_e(MKB_TemplateHelper::content_class('article')); ?>"><?php

			while (have_posts()) : the_post(); // main loop

				?><div id="mkb-article-<?php the_ID(); ?>"><?php

					do_action('minerva_single_title_before');

					?><header class="mkb-page-header"><?php

						do_action('minerva_single_title_inside_before');

						the_title( '<h1 class="mkb-page-title">', '</h1>' );

						do_action('minerva_single_title_inside_after');

					?></header><!-- .mkb-entry-header --><?php

					do_action('minerva_single_title_after');

					?><div class="mkb-single-content"><?php

						do_action('minerva_single_content_inside_before');

						?><div class="mkb-single-content__featured"><?php

							do_action('minerva_single_featured_before');

							the_post_thumbnail();

							do_action('minerva_single_featured_after');

						?></div><?php

						?><div class="mkb-single-content__text"><?php

							do_action('minerva_single_text_before');

							MKB_TemplateHelper::single_content();

							do_action('minerva_single_text_after');

						?></div><?php

						do_action('minerva_single_content_inside_after');

					?></div><!-- .mkb-single-content --><?php

					do_action('minerva_single_content_after');

					?></div><!-- #mkb-article-## --><?php

			endwhile;

			?></div><!--.mkb-content-main--><?php

		MKB_TemplateHelper::maybe_render_right_sidebar( 'article' );

		?></div><!--.mkb-container--><?php

get_footer();

?>
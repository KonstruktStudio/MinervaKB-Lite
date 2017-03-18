<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

$article_icon = MKB_Options::option('article_icon');

?><div id="mkb-article-<?php the_ID(); ?>" class="mkb-article-item mkb-article-item--simple"><?php

	do_action('minerva_loop_entry_before');

	?><header class="mkb-entry-header"><?php

		do_action('minerva_loop_entry_inside_before');

		the_title(
			sprintf( '<h2 class="mkb-entry-title"><i class="mkb-article-icon fa fa-lg ' .
			         esc_attr($article_icon) . '"></i><a href="%s" rel="bookmark">',
				esc_url( get_permalink() ) ),
			'</a></h2>' );

		do_action('minerva_loop_entry_inside_after');

	?></header><!-- .mkb-entry-header --><?php

	do_action('minerva_loop_entry_after');

?></div><!-- #mkb-article-## -->
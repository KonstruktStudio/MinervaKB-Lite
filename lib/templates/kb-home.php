<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

MKB_TemplateHelper::render_search();

if (MKB_Options::option( 'home_topics' )) {
	$ids = explode(',', MKB_Options::option( 'home_topics' ));
	$topics = array();

	foreach ($ids as $id) {
		$topic = get_term_by('id', (int)$id, MKB_Options::option( 'article_cpt_category' ));
		array_push($topics, $topic);
	}
} else {
	$topics = get_terms( MKB_Options::option( 'article_cpt_category' ), array(
		'hide_empty' => true,
	) );
}

$columns = MKB_TemplateHelper::get_home_columns(MKB_Options::option('home_layout'));
$view_mode = MKB_Options::option( 'home_view' );
$row_open = false;

?><div class="mkb-home-topics mkb-container mkb-columns mkb-columns-<?php echo esc_attr($columns); ?>">
	<?php
	if ( sizeof( $topics ) ):
		$i = 0;

		foreach ( $topics as $topic ):

			if ($i % $columns === 0):
				echo '<div class="mkb-row">';
				$row_open = true;
			endif;

			if ($view_mode === 'list'):
				MKB_TemplateHelper::render_as_list($topic);
			else:
				MKB_TemplateHelper::render_as_box($topic);
			endif;

			if ( ($i + 1) % $columns === 0 ):
				echo '</div >';
				$row_open = false;
			endif;

		++$i;
		endforeach; // end of terms loop

		if ( $row_open ):
			echo '</div >';
			$row_open = false;
		endif;

	endif; // end of topics loop
?></div>
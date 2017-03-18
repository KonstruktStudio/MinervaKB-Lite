<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

require_once(MINERVA_KB_PLUGIN_DIR . 'lib/helpers/icon-options.php');

class MKB_SettingsBuilder {

	private $no_tabs = false;

	public function __construct($args = null) {
		if (isset($args) && isset($args['no_tabs'])) {
			$this->no_tabs = $args['no_tabs'];
		}
	}

	protected $tab_open = false;

	public function render_option( $type, $value, $config ) {
		switch ( $type ) {
			case 'checkbox':
				$this->toggle( $value, $config );
				break;

			case 'input':
				$this->input( $value, $config );
				break;

			case 'textarea':
				$this->textarea( $value, $config );
				break;

			case 'color':
				$this->color( $value, $config );
				break;

			case 'select':
				$this->select( $value, $config );
				break;

			case 'page_select':
				$this->page_select( $value, $config );
				break;

			case 'icon_select':
				$this->icon_select( $value, $config );
				break;

			case 'image_select':
				$this->image_select( $value, $config );
				break;

			case 'layout_select':
				$this->layout_select( $value, $config );
				break;

			case 'tab':
				$this->open_tab_container( $config );
				break;

			case 'title':
				$this->title( $value, $config );
				break;

			case 'code':
				$this->code( $value, $config );
				break;

			case 'css_size':
				$this->css_size( $value, $config );
				break;

			default:
				break;
		}
	}

	public function render_tab_links( $options ) {
		$tabs = array_filter( $options, function ( $option ) {
			return $option["type"] === "tab";
		} );
		?>
		<div class="mkb-settings-tabs">
			<ul>
				<?php
				foreach ( $tabs as $tab ):
					?>
					<li class="mkb-settings-tab">
						<a href="#mkb_tab-<?php echo esc_attr( $tab["id"] ); ?>">
							<i class="mkb-settings-tab__icon fa fa-lg <?php echo esc_attr($tab["icon"]); ?>"></i>
							<?php echo esc_html( $tab["label"] ); ?>
						</a>
					</li>
				<?php
				endforeach;
				?>
			</ul>
		</div>
	<?php
	}

	protected function open_tab_container( $config ) {
		if ( $this->tab_open ) {
			$this->close_tab_container();
		}

		$this->tab_open = true;
		?>
		<div id="mkb_tab-<?php echo esc_attr( $config["id"] ); ?>" class="mkb-settings-tab__container">
	<?php
	}

	public function close_tab_container() {
		?></div><?php
	}

	protected function render_label($config) {
		if (!array_key_exists('label', $config)) {
			return;
		}

		?>
		<label class="mkb-setting-label" for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
			<?php echo esc_html( $this->get_label( $config ) ); ?>
		</label>
	<?php
	}

	protected function render_description($config) {
		if (!array_key_exists('description', $config)) {
			return;
		}

		?>
		<div class="mkb-setting-description"><?php echo wp_kses_post($config["description"]); ?></div>
	<?php
	}

	protected function maybe_print_dependency_attribute($config) {
		if (!isset($config['dependency'])) {
			return;
		}

		echo esc_html(' data-dependency="');
		echo esc_attr(json_encode($config['dependency']));
		echo esc_html('"');
	}

	protected function get_id_key( $config ) {
		$postfix = '';

		if (isset($config['id_postfix'])) {
			$postfix = $config['id_postfix'];
		}

		return MINERVA_KB_OPTION_PREFIX . $config["id"] . $postfix;
	}

	protected function get_name_key( $config ) {
		return MINERVA_KB_OPTION_PREFIX . $config["id"];
	}

	protected function get_label( $config ) {
		return $config['label'];
	}

	protected function checkbox( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="checkbox"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</label>
			<input class="fn-control"
			       type="checkbox"
			       id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
			       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
				<?php if ( $value === true || $value === 'true' ) {
					echo 'checked="checked"';
				} ?>
				/>
			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function toggle( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="toggle"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>

			<div class="mkb-toggle-label">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</div>

			<div class="mkb-switch">
				<input id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
				       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
				       class="fn-control mkb-toggle mkb-toggle-round"
					<?php if ( $value === true || $value === 'true' ) {
						echo 'checked="checked"';
					} ?>
				       type="checkbox" />
				<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"></label>
			</div>

			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function input( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="input"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<?php $this->render_label($config); ?>
			<input class="fn-control"
			       type="text"
			       id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
			       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			       value="<?php echo esc_attr( $value ); ?>"
				/>
			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function textarea( $value, $config ) {

		$rows = isset($config["height"]) ? $config["height"] : 10;
		$cols = isset($config["width"]) ? $config["width"] : 60;

		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="textarea"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<?php $this->render_label($config); ?>
			<textarea class="fn-control"
			          class="mkb-settings-textarea"
			       id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
			       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			       rows="<?php esc_html_e($rows); ?>"
			       cols="<?php esc_html_e($cols); ?>"
				><?php echo wp_kses_post( $value ); ?></textarea>
			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function color( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="color"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</label>
			<input type="text"
			       class="mkb-color-picker fn-control"
			       id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
			       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			       value="<?php echo esc_attr( $value ); ?>"
				/>
			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function select( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="select"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</label>

			<select class="fn-control"
			        id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
			        name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>">
				<?php
				foreach ( $config["options"] as $key => $label ):
					?><option value="<?php echo esc_attr( $key ); ?>"<?php
					if ($key == $value) {echo ' selected="selected"'; }
					?>><?php echo esc_html( $label ); ?></option><?php
				endforeach;
				?>
			</select>
			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function page_select( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="page_select"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</label>

			<span class="mkb-page-select-wrap fn-page-select-wrap">
				<select class="fn-control"
				        id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
				        name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>">
					<?php
					foreach ( $config["options"] as $key => $label ):
						?><option value="<?php echo esc_attr( $key ); ?>"<?php
						if ($key == $value) {echo ' selected="selected"'; }
						?> data-link="<?php esc_attr_e(get_the_permalink($key)); ?>"><?php echo esc_html( $label ); ?></option><?php
					endforeach;
					?>
				</select>
				<a class="mkb-page-select-link fn-page-select-link mkb-unstyled-link mkb-disabled" href="#" target="_blank">
					<?php _e( 'Open page', 'minerva-kb' ); ?> <i class="fa fa-external-link-square"></i>
				</a>
			</span>
			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function icon_select( $value, $config ) {
		$icon_options = mkb_icon_options();

		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="icon_select"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</label>

			<input class="fn-control mkb-icon-hidden-input" type="hidden"
			       id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
			       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			       value="<?php echo esc_attr( $value ); ?>"
				/>

			<div class="mkb-icon-button">
				<a href="#" class="mkb-icon-button__link mkb-button mkb-unstyled-link">
					<i class="mkb-icon-button__icon fa fa-lg <?php echo esc_attr( $value ); ?>"></i>
					<span class="mkb-icon-button__text"><?php echo esc_html( $value ); ?></span>
				</a>
			</div>
			<div class="mkb-icon-select mkb-hidden">
				<?php
				foreach ( $icon_options as $key => $label ):
					?>
					<span data-icon="<?php echo esc_attr($key); ?>" class="mkb-icon-select__item<?php if ($key == $value) { echo ' mkb-icon-selected'; } ?>">
						<i class="fa fa-lg <?php echo esc_attr($key); ?>"></i>
					</span>
				<?php
				endforeach;
				?>
			</div>
			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function image_select( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="image_select"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</label>

			<input class="fn-control mkb-image-hidden-input" type="hidden"
			       id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
			       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			       value="<?php echo esc_attr( $value ); ?>"
				/>

			<div class="mkb-image-select">
				<ul>
					<?php
					foreach ( $config["options"] as $key => $item ):
						?>
						<li data-value="<?php echo esc_attr( $key ); ?>"
						    class="mkb-image-select__item<?php
						if ($key == $value) {echo ' mkb-image-selected'; } ?>">
							<span class="mkb-image-wrap">
								<img src="<?php echo esc_attr($item["img"]); ?>"
							     class="mkb-image-select__image" />
								<span class="mkb-image-selected__checkmark">
									<i class="fa fa-lg fa-check-circle"></i>
								</span>
								</span>
							<span class="mkb-image-select__item-label">
								<?php echo esc_html( $item["label"] ); ?>
							</span>
						</li>
					<?php
					endforeach;
					?>
				</ul>
			</div>

			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function layout_select( $value, $config ) {
		$options = $config['options'];

		$value = isset( $value ) && ! empty( $value ) ?
			array_map( function ( $item ) {
				return $item;
			}, explode( ",", $value ) ) :
			array();

		if (!empty($options)) {
			$available = array_filter($options, function($item) use ($value) {
				return !in_array($item['key'], $value);
			});

			$selected = array_filter($options, function($item) use ($value) {
				return in_array($item['key'], $value);
			});

			if (isset($selected) && !empty($selected)) {
				usort($selected, function($a, $b) use ($value) {
					return array_search($a['key'], $value) < array_search($b['key'], $value) ? -1 : 1;
				});
			}
		}

		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="layout_select"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</label>

			<input class="fn-control mkb-layout-hidden-input" type="hidden"
			       id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
			       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			       value="<?php echo esc_attr( implode(",", $value) ); ?>" />

			<div class="mkb-layout-select">

				<div class="mkb-layout-select__available mkb-layout-select__container">
					<?php
					if ( isset( $available ) && ! empty( $available ) ):
						foreach ( $available as $item ):
							?>
							<div data-value="<?php echo esc_attr( $item['key'] ); ?>"
							     class="mkb-layout-select__item">
								<?php echo esc_html( $item['label'] ); ?>
							</div>
						<?php
						endforeach;
					endif;
					?>
				</div>

				<div class="mkb-layout-select__selected mkb-layout-select__container">
					<?php
					if ( isset( $selected ) && ! empty( $selected ) ):
						foreach ( $selected as $item ):
							?>
							<div data-value="<?php echo esc_attr( $item['key'] ); ?>"
							     class="mkb-layout-select__item">
								<?php echo esc_html( $item['label'] ); ?>
							</div>
						<?php
						endforeach;
					endif;
					?>
				</div>
			</div>

			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function title( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="title"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<div class="mkb-settings-title-wrap">
				<div class="mkb-settings-title"><?php echo esc_html( $this->get_label( $config ) ); ?>
					<?php if(isset($config['preview_image'])): ?>
						<i class="mkb-settings-preview fa fa-eye"></i>
					<?php endif; ?>
				</div>
				<?php if(isset($config['preview_image'])): ?>
				<div class="mkb-setting-preview-image"
				     style="<?php if (isset($config['width'])) { echo esc_attr("width: " . $config['width'] . "px;"); } ?>">
					<img src="<?php echo esc_attr($config['preview_image']); ?>" alt="<?php echo esc_attr( $this->get_label( $config ) ); ?>"/>
				</div>
				<?php endif; ?>
			</div>

			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	protected function code( $value, $config ) {
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="code"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<div class="mkb-settings-code-wrap">
				<h3 class="mkb-code-title"><?php echo esc_html( $this->get_label( $config ) ); ?></h3>
				<code class="mkb-setting-code">
					<?php echo wp_kses_post($config["default"]); ?>
				</code>
			</div>

			<?php $this->render_description($config); ?>
		</div>
	<?php
	}

	/**
	 * CSS size
	 * @param $value
	 * @param $config
	 */
	protected function css_size( $value, $config ) {

		$units = array(
			'px' => 'px',
			'em' => 'em',
			'rem' => 'rem',
			'%' => '%'
		);

		if (isset($config["units"])) {
			$units = array_filter($units, function($value) use ($config){
				return in_array($value, $config["units"]);
			});
		}

		$default = $config['default'];

		$selected_unit = is_array($value) && isset($value["unit"]) ? $value["unit"] : $default['unit'];
		$selected_size = is_array($value) && isset($value["size"]) ? $value["size"] : $default['size'];
		?>
		<div class="mkb-control-wrap fn-control-wrap"
		     data-type="css_size"
		     data-name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
			<?php self::maybe_print_dependency_attribute($config); ?>>
			<label for="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>">
				<?php echo esc_html( $this->get_label( $config ) ); ?>
			</label>
			<div class="mkb-css-size">
				<input class="fn-css-size-value mkb-css-size__input"
				       type="text"
				       id="<?php echo esc_attr( $this->get_id_key( $config ) ); ?>"
				       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>"
				       value="<?php echo esc_attr( $selected_size ); ?>" /><?php
				?><ul class="mkb-css-size__units"><?php
					foreach($units as $unit):
						?><li><a href="#" class="fn-css-unit mkb-unstyled-link mkb-css-unit<?php if ($unit === $selected_unit) {
								esc_attr_e(' mkb-css-unit--selected');
							} ?>" data-unit="<?php esc_attr_e($unit); ?>"><?php esc_html_e($unit); ?></a></li><?php
					endforeach;
					?></ul>
				<input class="fn-css-size-unit-value mkb-css-size__unit-input"
				       type="hidden"
				       name="<?php echo esc_attr( $this->get_name_key( $config ) ); ?>_unit"
				       value="<?php echo esc_attr( $selected_unit ); ?>" />
			</div>

			<?php $this->render_description($config); ?>
		</div>
	<?php
	}
}
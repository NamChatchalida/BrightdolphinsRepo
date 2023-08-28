<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
use EACCustomWidgets\Core\Eac_Config_Elements;
?>
<form action="" method="POST" id="eac-form-settings" name="eac-form-settings">
	<div id="tab-1" style="display: none;">
		<div class="eac-settings-tabs">
			<div class="eac-elements__table-common">
				<?php
				ob_start();
				foreach ( Eac_Config_Elements::get_widgets_advanced_active() as $key => $active ) {
					if ( ! Eac_Config_Elements::are_widget_dependencies_enabled( $key ) ) {
						continue;
					}

					$widget_title  = Eac_Config_Elements::get_widget_title( $key );
					$href          = Eac_Config_Elements::get_widget_help_url( $key );
					$href_class    = Eac_Config_Elements::get_widget_help_url_class( $key );
					$class_wrapper = 'eac-elements__common-item widgets advanced' . Eac_Config_Elements::get_widget_badge_class( $key );

					if ( 'all-advanced' === $key ) {
						?>
						<div class="eac-elements__table-common header">
							<div class="eac-elements__common-item header">
								<span class="eac-elements__item-content header"><?php echo esc_html( $widget_title ); ?></span>
								<span>
									<label class="switch">
										<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php checked( 1, $active, true ); ?>>
										<div><div></div></div>
									</label>
								</span>
							</div>
						</div>
						<?php
					} else {
						?>
						<div class="<?php echo esc_attr( $class_wrapper ); ?>">
							<span class="eac-elements__item-content"><?php echo esc_html( $widget_title ); ?>
								<?php if ( ! empty( $href ) ) : ?>
									<a href="<?php echo esc_attr( $href ); ?>" target="_blank" rel="noopener noreferrer">
										<span class="<?php echo esc_attr( $href_class ); ?>"></span>
									</a>
								<?php endif; ?>
							</span>
							<span>
								<label class="switch">
									<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php checked( 1, $active, true ); ?>>
									<div><div></div></div>
								</label>
							</span>
						</div>
						<?php
					}
				}
				$output = ob_get_clean();
				echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div> <!-- Table common -->
		</div> <!-- Settings TAB -->
	</div> <!-- TAB 1-->

	<div id="tab-2" style="display: none;">
		<div class="eac-settings-tabs">
			<div class="eac-elements__table-common">
				<?php
				ob_start();
				foreach ( Eac_Config_Elements::get_widgets_common_active() as $key => $active ) {
					if ( ! Eac_Config_Elements::are_widget_dependencies_enabled( $key ) ) {
						continue;
					}

					$widget_title  = Eac_Config_Elements::get_widget_title( $key );
					$href          = Eac_Config_Elements::get_widget_help_url( $key );
					$href_class    = Eac_Config_Elements::get_widget_help_url_class( $key );
					$class_wrapper = 'eac-elements__common-item widgets common' . Eac_Config_Elements::get_widget_badge_class( $key );

					if ( 'all-components' === $key ) {
						?>
						<div class="eac-elements__table-common header">
							<div class="eac-elements__common-item header">
								<span class="eac-elements__item-content header"><?php echo esc_html( $widget_title ); ?></span>
								<span>
									<label class="switch">
										<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php checked( 1, $active, true ); ?>>
										<div><div></div></div>
									</label>
								</span>
							</div>
						</div>
						<?php
					} else {
						?>
						<div class="<?php echo esc_attr( $class_wrapper ); ?>">
							<span class="eac-elements__item-content"><?php echo esc_html( $widget_title ); ?>
								<?php if ( ! empty( $href ) ) : ?>
									<a href="<?php echo esc_attr( $href ); ?>" target="_blank" rel="noopener noreferrer">
										<span class="<?php echo esc_attr( $href_class ); ?>"></span>
									</a>
								<?php endif; ?>
							</span>
							<span>
								<label class="switch">
									<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php checked( 1, $active, true ); ?>>
									<div><div></div></div>
								</label>
							</span>
						</div>
						<?php
					}
				}
				$output = ob_get_clean();
				echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div> <!-- Table common -->
		</div> <!-- Settings TAB -->
	</div> <!-- TAB 2-->

	<div id="tab-3" style="display: none;">
		<div class="eac-settings-tabs">
			<div class="eac-elements__table-common">
			<?php
			ob_start();
			foreach ( Eac_Config_Elements::get_widgets_ehf_active() as $key => $active ) {
				if ( ! Eac_Config_Elements::are_widget_dependencies_enabled( $key ) ) {
					continue;
				}

				$widget_title  = Eac_Config_Elements::get_widget_title( $key );
				$href          = Eac_Config_Elements::get_widget_help_url( $key );
				$href_class    = Eac_Config_Elements::get_widget_help_url_class( $key );
				$class_wrapper = 'eac-elements__common-item widgets ehf' . Eac_Config_Elements::get_widget_badge_class( $key );

				if ( 'all-ehf' === $key ) {
					?>
					<div class="eac-elements__table-common header">
						<div class="eac-elements__common-item header">
							<span class="eac-elements__item-content header"><?php echo esc_html( $widget_title ); ?></span>
							<span>
								<label class="switch">
									<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php checked( 1, $active, true ); ?>>
									<div><div></div></div>
								</label>
							</span>
						</div>
					</div>
					<?php
				} else {
					?>
					<div class="<?php echo esc_attr( $class_wrapper ); ?>">
						<span class="eac-elements__item-content"><?php echo esc_html( $widget_title ); ?>
							<?php if ( ! empty( $href ) ) : ?>
								<a href="<?php echo esc_attr( $href ); ?>" target="_blank" rel="noopener noreferrer">
									<span class="<?php echo esc_attr( $href_class ); ?>"></span>
								</a>
							<?php endif; ?>
						</span>
						<span>
							<label class="switch">
								<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php checked( 1, $active, true ); ?>>
								<div><div></div></div>
							</label>
						</span>
					</div>
					<?php
				}
			}
			$output = ob_get_clean();
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			</div> <!-- Table common -->
		</div> <!-- Settings TAB -->
	</div> <!-- TAB 3-->

	<div class="eac-saving-box">
		<div id="eac-elements-to-save"><?php esc_html_e( 'Vous devez enregistrer les réglages', 'eac-components' ); ?></div>
		<input id="eac-sumit" type="submit" value="<?php esc_html_e( 'Enregistrer les modifications', 'eac-components' ); ?>">
		<div id="eac-elements-saved"></div>
		<div id="eac-elements-notsaved"></div>
	</div>
</form>

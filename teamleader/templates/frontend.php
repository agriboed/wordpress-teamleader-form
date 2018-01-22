<div class="teamleader-container" id="teamleaderContainer<?php echo esc_attr( $id ); ?>">
    <form id="tmForm<?php echo $id; ?>">
		<?php foreach ( $fields as $key => $field ): ?>
            <div class="form-group">
                <label>
					<?php if ( $field['type'] !== 'hidden' ) {
						echo esc_html( $field['label'] );
					}

					$required = $field['required'] ? 'required=required' : '';

					switch ( $field['type'] ) {
						case 'textarea':
							echo '<textarea name="' . esc_attr( $key ) . '" placeholder="' . esc_html( $field['label'] ) . '" ' . $required . ' class="form-control">' . esc_attr( $field['value'] ) . '</textarea>';
							break;
						case 'number':
							echo '<input type="number" name="' . esc_attr( $key ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_html( $field['label'] ) . '" ' . $required . ' class="form-control">';
							break;
						case 'boolean':
							$checked = ! empty( $field['value'] ) ? 'checked' : '';
							echo '<input type="checkbox" name="' . esc_attr( $key ) . '" ' . $required . ' ' . $checked . ' class="form-control">';
							break;
						case 'hidden':
							echo '<input type="hidden" name="' . esc_attr( $key ) . '" value=" ' . esc_attr( $field['value'] ) . '">';
							break;
						default:
							echo '<input type="text" name="' . esc_attr( $key ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_html( $field['label'] ) . '" ' . $required . ' class="form-control">';
							break;
					}
					?>
                </label>
            </div>
		<?php endforeach; ?>
		<?php if ( true === $options['logo'] ): ?>
            <div class="teamleader-powered"><?php _e( 'Powered by', 'teamleader' ); ?>
                <a href="http://referral.teamleader.eu/en?token=<?php echo ( ! empty( $options['referral_token'] ) ) ? esc_attr( $options['referral_token'] ) : 'refferal'; ?>"
                   target="_blank">Teamleader</a>
                <img src="<?php echo $logo; ?>" alt="<?php _e( 'Teamleader', $key ); ?>"></div>
		<?php endif; ?>

		<?php if ( true === $options['recaptcha']['enable'] ): ?>
            <div class="g-recaptcha" data-sitekey="<?php echo isset( $options['recaptcha']['key'] ) ? esc_attr( $options['recaptcha']['key'] ) : ''; ?>">
            </div>
		<?php endif; ?>
        <button type="submit"
                class="btn btn-primary teamleader-submit"><?php echo esc_html( $form['form']['submit'] ); ?></button>
    </form>
    <div class="teamleader-success" data-success
         style="display: none;"><?php echo esc_html( $form['form']['success'] ); ?></div>
    <div class="teamleader-error" data-error style="display: none;"><?php _e( 'Error sending form. Please contact system
        administrator.', 'teamleader' ); ?>
    </div>
</div>
<script>
    (function ($) {
        $(document).ready(function () {
            TeamleaderFront({
                id: '<?php echo esc_attr( $id );?>',
                url: '<?php echo admin_url( 'admin-ajax.php' );?>',
                nonce: '<?php echo wp_create_nonce( 'teamleader' );?>',
                container: $('#teamleaderContainer<?php echo esc_attr( $id ); ?>')
            })
        })
    }(jQuery))
</script>
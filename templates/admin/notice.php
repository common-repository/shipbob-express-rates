<?php
/**
 *   Renders an admin notice
 *
 * @var string $class
 * @var string $message
 */
?>
<div class="<?php echo esc_attr( $class ); ?>">
	<p><strong><?php echo nl2br( $message ); ?></strong></p>
</div>
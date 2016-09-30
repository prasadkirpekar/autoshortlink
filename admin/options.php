<div class='wrap'>
<h2>Shorte.st AutoShortLink</h2>
<form method='post' action=<?php echo $action_url ?>>
<?php wp_nonce_field('asl_nonce_action','asl_nonce_field'); ?>
<input type="hidden" name="submitted" value="1" />
<table class="form-table">


<tr>
	<th><p>Enter Your Shorte API key</p></th>
	<td><input type='text' name='service_key' value=<?php echo get_option('asl_api_key') ?> /></td>
</tr>
<tr>
	<td><?php submit_button(); ?></td>
	<td></td>
</tr>

</table>
</form>

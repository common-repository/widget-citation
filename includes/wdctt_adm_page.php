<?php

# checks the user's permissions and if it is not enabled, 
# the page will not be displayed. 

if ( !current_user_can( 'manage_options' ) ) {  return false; }

$pagelink = "?page=widget-citation&";

$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : false;

$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

$rem = isset( $_GET['rem'] ) ? absint( $_GET['rem'] ) : false;

if( $rem ) {

	wdctt_delete_item( $rem );

} else {

	$stat = isset( $_GET['stat'] ) ? absint( $_GET['stat'] ) : false;

	if( $stat !== false ) {

		wdctt_change_status( $id, $stat );

	}

}

?>

<div class='wdctt_wrap'>

	<div class='wdctt_box'>

		<div class='wdctt_brand clearfix'>

			<img src='<?php echo esc_url( plugins_url( 'img/citation-logo.png', dirname(__FILE__) ) );?>'>

		</div>

		<div class='clearfix'>

			<h3 class='wdctt_adm_h3'><?php _e('About','widget-citation'); ?></h3>

			<p>
			<?php _e('Plugin to show random citation widget in your WordPress blog.','widget-citation');?>
			</p>

			<ul>
			<li>
			<span class='dashicons dashicons-admin-users'></span>
			<span class='wdctt_items_adm'>
			<?php _e('Author: ', 'widget-citation'); ?>
			<a href='https://www.andrebrum.com.br/plugins'>Andre Brum Sampaio</a></span>
			</li>
			<li>
			<a href='https://www.andrebrum.com.br/' target='_blank'>
			<span class='dashicons dashicons-admin-links'></span>
			<span class='wdctt_items_adm'>
			<?php _e('Author URI: ','widget-citation');?>http://www.andrebrum.com.br/
			</span>
			</a>
			</li>
			<li>
			<a href='https://www.instagram.com/andrebrumsampaio/' target='_blank'>
			<span class='dashicons dashicons-instagram'></span>
			<span class='wdctt_items_adm'>
			<?php _e('Follow on Instagram: ','widget-citation');?>@andrebrumsampaio
			</span>
			</a>
			</li>
			<li>
			<a target='_blank' href='https://www.facebook.com/andrebrumsampaio/'>
			<span class='dashicons dashicons-facebook'></span>
			<span class='wdctt_items_adm'>
			<?php _e('Follow on Facebook: ','widget-citation');?>@andrebrumsampaio
			</span>
			</a>
			</li>

			<li>
			<span class='dashicons dashicons-update'></span>
			<span class='wdctt_items_adm'>
			<?php _e('Version: ','widget-citation');?>1.0
			</li>
			</ul>

		</div>


		<div class="clearfix">

			<h3 class='wdctt_adm_h3'><?php _e('Add New Message','widget-citation');?></h3>

			<div id='add_messages'>

				<?php 

				# Here we call our function that will check if 
				# there are any messages to be added to the database.

				$data = wdctt_new_message(); 

				if($data) extract($data);

				?>

				<form method="POST">			

				<input type="hidden" name="page" value="message_widget">

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><?php _e('Author','widget-citation');?></th>
							<td>
								<div class="wdctt_item-setting">
									<input type="text"  class="regular-text" name="wdctt_author" placeholder="<?php _e('Author','widget-citation');?>" value="<?php if(!empty($author)) echo $author;?>">
								</div>

								

							</td>
						</tr>

						<tr>
							<th scope="row"><?php _e('Message','widget-citation');?></th>
							<td>
								<div class="wdctt_item-setting">
								<textarea name="wdctt_message" class="regular-text" placeholder="<?php _e('Message','widget-citation');?>"><?php if(!empty($message) ) echo $message; ?></textarea>
								</div>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Add Message','widget-citation');?>"></p>

				</form>

			</div>

		</div>


		<div class="clearfix">

			<h3 id='citation_section_title' class='wdctt_adm_h3'>
				<?php _e('Mensagens','widget-citation'); ?>
			</h3>


			<div id='citation_section_content'>

				<table class="wp-list-table widefat fixed striped">

					<thead>

						<tr>
							<th scope="col" class="">
								<?php _e('Author', 'widget-citation'); ?>
							</th>
							<th scope="col" class="">
								<?php _e('Citation', 'widget-citation'); ?>
							</th>
							<th scope="col" class="">
								<?php _e('Actions', 'widget-citation'); ?>
							</th>

						</tr>

					</thead>

					<tbody>

						<?php 
						
						$data = wdctt_list_citation(); 

						if( $data ) {


							foreach ( $data['m'] as $key => $value ) {

								extract($value);

								if( $status == 1 ) {

									$stat = 0;
									$stat_class = 'wdctt_enable';
									$stat_name = __('disable', 'widget-citation');

								} else {

									$stat = 1;
									$stat_class = 'wdctt_disable';
									$stat_name = __('enable', 'widget-citation');

								} 

								echo "<tr id='citation-$id'>";
								echo "<td id='author' class=''>$author</td>";
								echo "<td id='message' class=''>$msg</td>";
								echo "<td id='act' class=''>";
								echo "<a class='wdctt_delete' href='". $_SERVER['PHP_SELF'] . $pagelink . "&rem=$id&pagenum=$pagenum/#citation_section_content'>". __('delete','widget-citation') ."</a>";
								echo " | ";
								echo "<a class='$stat_class' href='" . $_SERVER['PHP_SELF'] . $pagelink . "stat=$stat&id=$id&pagenum=$pagenum/#citation_section_content'>$stat_name</a>";
								echo "</td>";
								echo "</tr>";

							}
						}

						?>

					</tbody>

				</table>

				<?php echo $data['p']; ?>

			</div>

		</div>

	</div>
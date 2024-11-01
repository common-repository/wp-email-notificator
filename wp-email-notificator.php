<?
/**
 * @package WP-Email-Notificator
 * @version 0.1
 */
/*
Plugin Name: WordPress Email Notificator
Plugin URI: http://wordpress.org/extend/plugins/wp-email-notificator/
Description: Email notification for posts and comments in the WordPress.com way for .org, with some extra surprises.
Author: Rafael Poveda - RaveN from Mecus.es
Version: 0.2
Author URI: http://mecus.es/author/raven
Contributors: bi0xid, _dorsvenabili, mecus
*/


function wp_notify_mail($post_id){

	global $wpdb;
	
	if (is_multisite()){
		global $blog_id;
		$usermeta = 'wp_'.$blog_id.'_user_level';
	} else {
		$usermeta = 'wp_user_level';
	}
	
	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$post_id' LIMIT 1");
	$first_author = $wpdb->get_var("SELECT display_name FROM $wpdb->users WHERE ID = '$post->post_author'");
	$blogname = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'blogname'");
	$users = $wpdb->get_results("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$usermeta'");
	foreach ($users as $user) {
		$email = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE ID = '$user->user_id'");
		$emails .= $email.',';
	}


$notify_message .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style type="text/css" media="all">
	a:hover {	color: red;	}
	a {
		text-decoration: none;
		color: #0088cc;
	}
	
	a.primaryactionlink:link, a.primaryactionlink:visited { background-color: #2585B2; color: #fff; }
	a.primaryactionlink:hover, a.primaryactionlink:active { background-color: #11729E !important; color: #fff !important; }

/*
 	@media only screen and (max-device-width: 480px) { 
		 .post { min-width: 700px !important; }
	}
*/
	</style>
	<title>Bach Ticket System</title>
	<!--[if gte mso 12]>
	<style type="text/css" media="all">
	body {
	font-family: arial;
	font-size: 0.8em;
	}
	.post, .comment {
	background-color: white !important;
	line-height: 1.4em !important;
	}
	</style>	
	<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>';

$notify_message .= '	<div style="max-width: 1024px; min-width: 600px;" class="content">
		<div style="padding: 1em; margin: 1.5em 1em 0.5em 1em; background-color: #f5f5f5; border: 1px solid #ccc; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; line-height: 1.6em;" class="post">
	<table style="width: 100%;" class="post-details">
		<tr>
			<td valign="top">
				<h2 style="margin: 0; font-size: 1.6em; color: #555;" class="post-title">
					<a href="'.$post->guid.'" style="text-decoration: none; color: #0088cc;margin-bottom:10px;">'.$post->post_title.'</a>
				</h2>
					
';
	
				
$notify_message .= '									<div style="color: #999; font-size: 0.9em; margin-top: 4px;margin-bottom:10px;" class="meta">
						<strong>'.$first_author.'</strong>';

			$fecha = $post->post_date;
			$fecha = date_parse_from_format("Y-n-j H:i:s", $fecha); if ($fecha[minute] < 10) { $fecha[minute] = '0'.$fecha[minute]; }
			$notify_message .= '<br /><span style="color:#aaa;">'.$fecha[hour].':'.$fecha[minute].' '.$fecha[day].'/'.$fecha[month].'/'.$fecha[year].'</span></div>' ;
			$notify_message .= '<div style="color: #000;">';
			$notify_message .= nl2br($post->post_content) . '<br /><br />';
			$notify_message .= sprintf( __('Enlace:  %1$s'), get_permalink($post->ID));
			$notify_message .= '</div>';
$notify_message .= '	</body>
</html>
'; 





		$from = "From: Bach Mecus <bach@mecus.es>";
		$message_headers = "MIME-Version: 1.0\n" . "$from\n" . "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";

	$title = $post->post_title;
	$subject = 'Nueva entrada en ' .$blogname.': «'.$title.'»';	


	//$emails = "raven@mecus.es";
	$emails_array = explode(",", $emails);
		
		foreach ($emails_array as $email) {
			wp_mail($email, $subject, $notify_message, $message_headers);
		}

}

function wp_notify_comments($comment_id) {
	global $wpdb;

	if (is_multisite()){
		global $blog_id;
		$usermeta = 'wp_'.$blog_id.'_user_level';
	} else {
		$usermeta = 'wp_user_level';
	}

	
	$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id'  AND comment_approved = '1' LIMIT 1");
	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");
	$first_author = $wpdb->get_var("SELECT display_name FROM $wpdb->users WHERE ID = '$post->post_author'");
	$other_comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_parent = '0' AND comment_approved = '1' ORDER BY comment_ID ASC");
	$blogname = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'blogname'");
	$users = $wpdb->get_results("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$usermeta'");
	foreach ($users as $user) {
		$email = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE ID = '$user->user_id'");
		$emails .= $email.',';
	}


$notify_message .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style type="text/css" media="all">
	a:hover {	color: red;	}
	a {
		text-decoration: none;
		color: #0088cc;
	}
	
	a.primaryactionlink:link, a.primaryactionlink:visited { background-color: #2585B2; color: #fff; }
	a.primaryactionlink:hover, a.primaryactionlink:active { background-color: #11729E !important; color: #fff !important; }

/*
 	@media only screen and (max-device-width: 480px) { 
		 .post { min-width: 700px !important; }
	}
*/
	</style>
	<title>Bach Ticket System</title>
	<!--[if gte mso 12]>
	<style type="text/css" media="all">
	body {
	font-family: arial;
	font-size: 0.8em;
	}
	.post, .comment {
	background-color: white !important;
	line-height: 1.4em !important;
	}
	</style>	
	<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>';

$notify_message .= '	<div style="max-width: 1024px; min-width: 600px;" class="content">
		<div style="padding: 1em; margin: 1.5em 1em 0.5em 1em; background-color: #f5f5f5; border: 1px solid #ccc; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; line-height: 1.6em;" class="post">
	<table style="width: 100%;" class="post-details">
		<tr>
			<td valign="top">
				<h2 style="margin: 0; font-size: 1.6em; color: #555;" class="post-title">
					<a href="'.$post->guid.'" style="text-decoration: none; color: #0088cc;margin-bottom:10px;">'.$post->post_title.'</a>
				</h2>
					
';
	
				
$notify_message .= '									<div style="color: #999; font-size: 0.9em; margin-top: 4px;margin-bottom:10px;" class="meta">
						<strong>'.$first_author.'</strong>';

			// THE POST
			$fecha = $post->post_date;
			$fecha = date_parse_from_format("Y-n-j H:i:s", $fecha); if ($fecha[minute] < 10) { $fecha[minute] = '0'.$fecha[minute]; }
			$notify_message .= '<br /><span style="color:#aaa;">'.$fecha[hour].':'.$fecha[minute].' '.$fecha[day].'/'.$fecha[month].'/'.$fecha[year].'</span><br /></div>' ;
			$notify_message .= '<div style="color: #999;">';
			$notify_message .= nl2br($post->post_content) . '<br /><br />';
			
			// COMMENTS
			$notify_message .= '<ul>';
			foreach ($other_comments as $comment){
				$notify_message .= '<li style="list-style-type: none">';
				
				// IF THIS IS THE LAST COMMENT, WE COLOUR IT
				if ($comment->comment_ID == $comment_id){
					$notify_message .= '<div style="color:black;">';
				}
				
				$fecha = $comment->comment_date;
				$fecha = date_parse_from_format("Y-n-j H:i:s", $fecha); if ($fecha[minute] < 10) { $fecha[minute] = '0'.$fecha[minute]; }
				$notify_message .= '<span style="color:#aaa;margin-left:-10px;">'.$fecha[hour].':'.$fecha[minute].' '.$fecha[day].'/'.$fecha[month].'/'.$fecha[year].'</span><br />' ;

				$notify_message .= sprintf( __('<strong>%s:</strong>'), $comment->comment_author ) ;
				$notify_message .= "<br /><br />" . nl2br($comment->comment_content) . "<br /><br />";
				
				
				// IF IT HAS REPLIES
				$children = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_parent = '$comment->comment_ID' AND comment_approved = '1'  ORDER BY comment_ID ASC");
	
				$notify_message .= '<ul>';
				foreach ($children as $child){
					if ($child->comment_ID == $comment_id){
						$notify_message .= '<div style="color:black;">';
					}

					$notify_message .= '<li style="list-style-type: none;border-left:3px solid #999;margin-left:15px;padding-left:15px;">';
					
					$fecha = $child->comment_date;
					$fecha = date_parse_from_format("Y-n-j H:i:s", $fecha); if ($fecha[minute] < 10) { $fecha[minute] = '0'.$fecha[minute]; }
					$notify_message .= '<span style="color:#aaa;margin-left:-10px;">'.$fecha[hour].':'.$fecha[minute].' '.$fecha[day].'/'.$fecha[month].'/'.$fecha[year].'</span><br />' ;
					
					$notify_message .= sprintf( __('<strong>%s:</strong>'), $child->comment_author ) ;
					$notify_message .= "<br /><br />" . nl2br($child->comment_content) . "<br /><br />";
								
								
								
					// IF REPLIES HAVE REPLIES			
						$second = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_parent = '$child->comment_ID' AND comment_approved = '1'  ORDER BY comment_ID ASC");
					
						$notify_message .= '<ul>';
						foreach ($second as $sec){
							if ($sec->comment_ID == $comment_id){
								$notify_message .= '<div style="color:black;">';
							}
				
							$notify_message .= '<li style="list-style-type: none;border-left:3px solid #999;margin-left:15px;padding-left:15px;">';
							
							$fecha = $sec->comment_date;
							$fecha = date_parse_from_format("Y-n-j H:i:s", $fecha); if ($fecha[minute] < 10) { $fecha[minute] = '0'.$fecha[minute]; }
							$notify_message .= '<span style="color:#aaa;margin-left:-10px;">'.$fecha[hour].':'.$fecha[minute].' '.$fecha[day].'/'.$fecha[month].'/'.$fecha[year].'</span><br />' ;
							
							$notify_message .= sprintf( __('<strong>%s:</strong>'), $sec->comment_author ) ;
							$notify_message .= "<br /><br />" . nl2br($sec->comment_content) . "<br /><br />";


						// IF REPLIES OF REPLIES HAVE REPLIES


						$third = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_parent = '$sec->comment_ID'  AND comment_approved = '1' ORDER BY comment_ID ASC");
					
						$notify_message .= '<ul>';
						foreach ($third as $thi){
							if ($thi->comment_ID == $comment_id){
								$notify_message .= '<div style="color:black;">';
							}
				
							$notify_message .= '<li style="list-style-type: none;border-left:3px solid #999;margin-left:15px;padding-left:15px;">';
							
							$fecha = $thi->comment_date;
							$fecha = date_parse_from_format("Y-n-j H:i:s", $fecha); 
							if ($fecha[minute] < 10) { $fecha[minute] = '0'.$fecha[minute]; }
							$notify_message .= '<span style="color:#aaa;margin-left:-10px;">'.$fecha[hour].':'.$fecha[minute].' '.$fecha[day].'/'.$fecha[month].'/'.$fecha[year].'</span><br />' ;
							
							$notify_message .= sprintf( __('<strong>%s:</strong>'), $thi->comment_author ) ;
							$notify_message .= "<br /><br />" . nl2br($thi->comment_content) . "<br /><br />";

							// IF REPLIES OF REPLIES OF REPLIES HAVE REPLIES
		
		
							$fourth = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_parent = '$thi->comment_ID' AND comment_approved = '1'  ORDER BY comment_ID ASC");
						
							$notify_message .= '<ul>';
							foreach ($fourth as $fou){
								if ($fou->comment_ID == $comment_id){
									$notify_message .= '<div style="color:black;">';
								}
					
								$notify_message .= '<li style="list-style-type: none;border-left:3px solid #999;margin-left:15px;padding-left:15px;">';
								
								$fecha = $fou->comment_date;
								$fecha = date_parse_from_format("Y-n-j H:i:s", $fecha); if ($fecha[minute] < 10) { $fecha[minute] = '0'.$fecha[minute]; }
								$notify_message .= '<span style="color:#aaa;margin-left:-10px;">'.$fecha[hour].':'.$fecha[minute].' '.$fecha[day].'/'.$fecha[month].'/'.$fecha[year].'</span><br />' ;
								
								$notify_message .= sprintf( __('<strong>%s:</strong>'), $fou->comment_author ) ;
								$notify_message .= "<br /><br />" . nl2br($fou->comment_content) . "<br /><br />";
		
								// IF REPLIES OF REPLIES OF REPLIES OF REPLIES HAVE REPLIES
			
			
								$five = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_parent = '$fou->comment_ID' AND comment_approved = '1' ORDER BY comment_ID ASC");
							
								$notify_message .= '<ul>';
								foreach ($five as $fiv){
									if ($fiv->comment_ID == $comment_id){
										$notify_message .= '<div style="color:black;">';
									}
						
									$notify_message .= '<li style="list-style-type: none;border-left:3px solid #999;margin-left:15px;padding-left:15px;">';
									
									$fecha = $fiv->comment_date;
									$fecha = date_parse_from_format("Y-n-j H:i:s", $fecha); if ($fecha[minute] < 10) { $fecha[minute] = '0'.$fecha[minute]; }
									$notify_message .= '<span style="color:#aaa;margin-left:-10px;">'.$fecha[hour].':'.$fecha[minute].' '.$fecha[day].'/'.$fecha[month].'/'.$fecha[year].'</span><br />' ;
									
									$notify_message .= sprintf( __('<strong>%s:</strong>'), $fiv->comment_author ) ;
									$notify_message .= "<br /><br />" . nl2br($fiv->comment_content) . "<br /><br />";
			
			
			
								// CLOSE REPLIES OF REPLIES OF REPLIES OF REPLIES OF REPLIES
									$notify_message .= '</li>';
			
									if ($fiv->comment_ID == $comment_id){
										$notify_message .= '</div>';
									}
								}
								$notify_message .= '</ul>';
			
		
		
							// CLOSE REPLIES OF REPLIES OF REPLIES OF REPLIES
								$notify_message .= '</li>';
		
								if ($fou->comment_ID == $comment_id){
									$notify_message .= '</div>';
								}
							}
							$notify_message .= '</ul>';
		


						// CLOSE REPLIES OF REPLIES OF REPLIES
							$notify_message .= '</li>';

							if ($thi->comment_ID == $comment_id){
								$notify_message .= '</div>';
							}
						}
						$notify_message .= '</ul>';



						// CLOSE REPLIES OF REPLIES
						$notify_message .= '</li>';

						if ($sec->comment_ID == $comment_id){
							$notify_message .= '</div>';
						}
					}
					$notify_message .= '</ul>';
					
					
					
					// CLOSE REPLIES
					$notify_message .= '</li>';

					if ($child->comment_ID == $comment_id){
						$notify_message .= '</div>';
					}

				}
				$notify_message .= '</ul>';

				// CLOSE COMMENTS
				if ($comment->comment_ID == $comment_id){
					$notify_message .= '</div>';
				}
				$notify_message .= '</li>';
			}
			$notify_message .= '</ul>';
			
			// THE LINK
			$notify_message .= sprintf( __('Enlace:  %1$s'), get_permalink($comment->comment_post_ID));
			$notify_message .= '</div>';
$notify_message .= '	</body>
</html>
'; 
	

	$title = $post->post_title;
	$subject = 'Nuevo comentario en «'.$title.'» | '.$blogname;	

		$from = "From: Change Me <bach@example.org>";
		$message_headers = "MIME-Version: 1.0\n" . "$from\n" . "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
		
//	$emails = "raven@mecus.es";
	$emails_array = explode(",", $emails);
		
		foreach ($emails_array as $email) {
			wp_mail($email, $subject, $notify_message, $message_headers);
		}

}


/* Creamos ahora la función que nos notificará en los comentarios y al publicar */

add_action('publish_post', 'wp_notify_mail'); // Esta función está llamada en index.php
add_action('comment_post', 'wp_notify_comments');
add_action('edit_comment', 'wp_notify_comments');


?>
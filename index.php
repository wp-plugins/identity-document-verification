<?php

	/**
	* Plugin Name: Identity Document Verification
	* Description: You can position this API anywhere on your website or inside a form and customise the look and feel to suit the style of your website.n Australia the Document Verification API will allow you to identify an individual though the submission of Driving Licence/Passport.
	* Author: Identity Verification Services
	* Version: 1.0
	* Author URI: https://profiles.wordpress.org/identity-verification-services
	*/

	
	// Plugin Activation

	register_activation_hook( __FILE__,'identity_documentactivation');
	function identity_documentactivation(){
		global $wpdb;
		$identity_documenttable=$wpdb->prefix."identity_documentconfigurations";
		if($wpdb->get_var("SHOW TABLES LIKE '$identity_documenttable'") != $identity_documenttable) {
			$sql="CREATE TABLE $identity_documenttable(
					configuration_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
					client_id varchar(150),
					client_secret varchar(150),
					redirect_url varchar(100),
					error_url varchar(100)
				);";
		 	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sql );
		}
	}


	// Plugin Deactivation

	register_deactivation_hook( __FILE__,'identity_documentdeactivation');
	function identity_documentdeactivation(){
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."identity_documentconfigurations");
	}


	// Admin Menu 

	add_action("admin_menu","identity_documentadmin_menu");

	function identity_documentadmin_menu(){
		add_menu_page("Idenity Document Verification","Idenity Document Verification","manage_options","identity_document_verification","identity_document_verification_form");
	}


	// Configuration Form

	function identity_document_verification_form(){
		global $wpdb;
		if($_POST){
			
			$configuration=$wpdb->get_results("select * from ".$wpdb->prefix ."identity_documentconfigurations");
			if(count($configuration)>0)
				$wpdb->query("update ".$wpdb->prefix."identity_documentconfigurations set client_id='".$_POST['client_id']."',client_secret='".$_POST['client_secret']."',redirect_url='".$_POST['redirect_url']."',error_url='".$_POST['error_url']."' where configuration_id=".$configuration[0]->configuration_id);
			else	
			$wpdb->insert($wpdb->prefix ."identity_documentconfigurations",$_POST);
		}
		$configuration=$wpdb->get_results("select * from ".$wpdb->prefix."identity_documentconfigurations");
		include("configuration_form.php");
	}


	// Form for PHOTO ID Verification

	function identity_documentverification_form(){
		global $wpdb;
		$configuration=$wpdb->get_results("select * from ".$wpdb->prefix ."identity_documentconfigurations");
		include("verification_form.php");

	}

	add_shortcode("IVS_IDENTITY_DOCUMENT_VERIFICATION","identity_documentverification_form");

	// Loading Styles

	add_action("wp_enqueue_scripts","identity_documentstyles");
	add_action("admin_enqueue_scripts","identity_documentstyles");


	function identity_documentstyles(){

		wp_register_style("identity_documentstyles",plugins_url("identity_documentstyles/identity_documentstyles.css", __FILE__));
		wp_enqueue_style("identity_documentstyles");
	}


	// Loading Scripts

	add_action("wp_enqueue_scripts","identity_documentscripts");

	function identity_documentscripts(){
		wp_enqueue_script("jquery");
		wp_register_script("identity_documentscripts",plugins_url("identity_documentscripts/validation.js", __FILE__));
		wp_enqueue_script("identity_documentscripts");
	}



	// Ajax Calls for API Calls
	add_action("wp_ajax_photo_id_verify", "identity_documentphoto_id_document_verification");
	add_action("wp_ajax_nopriv_photo_id_verify", "identity_documentphoto_id_document_verification");

	function identity_documentphoto_id_document_verification(){

		global $wpdb;
		$url='http://staging-api.identityverification.com/get_verified/get_auth_token/';
		$configuration=$wpdb->get_results("select * from ".$wpdb->prefix ."identity_documentconfigurations");
		$config_auth['client_id']=$configuration[0]->client_id;
		$config_auth['client_secret']=$configuration[0]->client_secret;
		
		$auth_token_result=identity_documentsendPostData_api($url,json_encode($config_auth));


		// PHOTO ID Document Verification 
		$config_details['auth_token']=$auth_token_result->auth_token;
		
		


		if($_POST['photoid_url']!=''){
			$config_details['identity_document_url']=$_POST['photoid_url'];

		}else{
			 $upload_dir = wp_upload_dir();
			// echo "<pre>";print_r($upload_dir);
			 $image=str_replace(" ","_",$_FILES['photo']['name']);
			copy($_FILES['photo']['tmp_name'],$upload_dir['basedir']."/".$image);
			$config_details['identity_document_url']=$upload_dir['baseurl']."/".$image;
		}
		$config_details['identity_type']=$_POST['identity_type'];
		$config_details['country']=ucfirst(strtolower($_POST['country']));

		 $photoid_verification_url='http://staging-api.identityverification.com/get_verified/identity_document';
		 
		$response=identity_documentsendPostData_api($photoid_verification_url,json_encode($config_details));
		
		 $result=json_encode($response);
		// echo "<pre>";print_r($response);
		//exit;
		$redirect_url=$api_credentials[0]->redirect_url;
		$error_url=$api_credentials[0]->error_url;
		include("thankyou.php");
		exit;
	}


	function identity_documentsendPostData_api($url, $post){
		  $ch = curl_init($url);
		  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
		  $resulty = curl_exec($ch);
		  curl_close($ch);  // Seems like good practice
		  return json_decode($resulty);
	}



?>
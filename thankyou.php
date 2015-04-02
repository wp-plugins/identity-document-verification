<?php
/**
* Template Name:Thank You Page
* Description:This will page return the result
*/
//echo "<pre>";print_r($response);
?>

	<div class="content-element-1">
		<div id="content">
			<?php
			if($response->status=='Invalid'){
			?>
				<div style="padding:10px;marign-bottom:5px;">
					<h3><?php echo $response->message?></h3>
				</div>
			<?php
			}if($response->status=='valid' && $response->is_identity_document_verified!=''){
			?>
			<table class="response-tbl" >
					<!-- <tr>
						<th> Parameter</th>
						<th> Value</th>
						<th> Status</th>
					</tr> -->
					<tr>
						<td colspan="3" style="text-align:center"><h5><?php echo $response->message?></h5></td>
					</tr>
					<tr>

						<td>Identity Type</td>
						<td><?php echo ucwords(str_replace("_"," ",$response->identity_type))?></td>
						
					</tr>
					<tr>

						<td>First Name</td>
						<td><?php echo ucwords(str_replace("_"," ",$response->first_name))?></td>
						
					</tr>
					<tr>

						<td>Last Name</td>
						<td><?php echo ucwords(str_replace("_"," ",$response->last_name))?></td>
						
					</tr>
					<tr>

						<td>Date of Birth</td>
						<td><?php echo $response->date_of_birth?></td>
						
					</tr>
					<tr>

						<td>Country</td>
						<td><?php echo $response->country?></td>
						
					</tr>
					
					<tr>
						<td>
						Status
						</td>
						<td><?php echo ($response->is_identity_document_verified=='1'?'<img src="'.plugins_url("images/icons/tick_105.png" , __FILE__).'">':'<img src="'.plugins_url("images/icons/close-icon.gif" , __FILE__).'" style="width:19px;height:19px">')?></td>
					</tr>
				</table>
					<br/>
				<div>
					<form action="<?php echo ($response->is_identity_document_verified=='1'?$redirect_url:$error_url)?>" method="post"  style ="text-align:center">
						<input type="hidden" value='<?php echo json_encode($response)?>' name="response">
						<button type="submit" >CONTINUE</button>
						
						<br>
						<br>
					</form>
				</div>			
			<?php
			}else{

			?>
				<table class="response-tbl" >
					<tr>
						<td colspan="3" style="text-align:center"><h5><?php echo $response->message?></h5></td>
					</tr>
					<tr>

						<td>Identity Type</td>
						<td><?php echo ucwords(str_replace("_"," ",isset($response->identity_type)?$response->identity_type:$response->message))?></td>
						
					</tr>
				</table>
			<?php
			}
			?>
		</div>
	</div>
	<div class="clear"></div>



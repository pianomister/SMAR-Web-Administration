<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
*                                   *
*                                   *
************************************/

// extract file name
$self = 'devices.php';
$subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';

// check for type of call (direct/AJAX)
// on direct call, redirect to index for full layout
if(!isset($_GET['smar_include']) || $_GET['smar_include'] != 'true') {
	$url = $self;
	$url .= (strlen($_SERVER['QUERY_STRING']) != 0) ? '?'.$_SERVER['QUERY_STRING'] : '';
	header( 'location: index.php?page='.urlencode($url) );
} else {
	$topinclude = 0;
}

require_once('_functions/_functions.php');
require_once('inc_session_check.php');

if($_SESSION['loginRole'] < 50) {
	$SMAR_MESSAGES['error'][] = 'Insufficient permissions for device management.';
	smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES);
} else {

// include subnav if requested
if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true') {
	
	?>
	<nav id="nav-page">
		<ul>
			<li><a href="<?php echo $self; ?>" <?php echo ($subpage == '') ? 'class="smar-active"' : ''; ?>>Device manager</a></li>
			<li><a href="<?php echo $self.'?subpage=adddevice'; ?>" <?php echo ($subpage == 'adddevice') ? 'class="smar-active"' : ''; ?>>Add device</a></li>
		</ul>
	</nav>
	<?php
}
?>
<div id="smar-content-inner">
	<?php
	// print messages
	if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }

	// page content
	switch($subpage) {
		
		case 'adddevice':
			// form was sent
			if(isset($_POST['send_newdevice'])) {

				if(isset($_POST['add-device-name']) && !empty($_POST['add-device-name']) &&
					 isset($_POST['add-device-hwaddress']) && !empty($_POST['add-device-hwaddress'])
					) {

								$addName = strip_tags($_POST['add-device-name']);
								$addHWADDRESS = strip_tags($_POST['add-device-hwaddress']);
								if(isset($_POST['add-device-activated'])) {
									$addActivated = 1;
								} else {
									$addActivated = 0;
								}

								// init database
								if(!(isset($SMAR_DB))) {
									$SMAR_DB = new SMAR_MysqlConnect();
								}

								// get shelf data
								$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_device
																							(device_name, hwaddress, activated, created) VALUES
																							('".$SMAR_DB->real_escape_string($addName)."', '".$SMAR_DB->real_escape_string($addHWADDRESS)."', '".$SMAR_DB->real_escape_string($addActivated)."', NOW())");
								if($result === TRUE) {
									$SMAR_MESSAGES['success'][] = 'Device "'.$addName.'" was successfully created.';
								} else {
									$addFailed = true;
									$SMAR_MESSAGES['error'][] = 'Inserting the device "'.$addName.'" into database failed.';
								}
				} else {
					$addFailed = true;
					$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
				}
			}
			?>
			<h1>Add device</h1>
			<?php
			// print messages
			if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
			?>
			<p>* All form fields are required.</p>
			<form id="form-add-device" method="post" action="index.php?page=<?php echo urlencode($self.'?subpage=adddevice'); ?>">
				<div class="form-box swap-order">
					<input id="add-device-name" type="text" name="add-device-name" placeholder="Android Device" <?php if(isset($addFailed) && !empty($_POST['add-device-name'])) echo("value=\"".smar_form_input($_POST['add-device-name'])."\""); ?>/>
					<label for="add-device-name">Name</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-device-hwaddress" type="text" name="add-device-hwaddress" placeholder="00:00:00:00:00:00" <?php if(isset($addFailed) && !empty($_POST['add-device-hwaddress'])) echo("value=\"".smar_form_input($_POST['add-device-hwaddress'])."\""); ?>/>
					<label for="add-device-hwaddress">Hardware Address (MAC Address)</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-device-activated" type="checkbox" name="add-device-activated"<?php if(!isset($addFailed) || (isset($addFailed) && isset($_POST['add-device-activated']))) echo("checked"); ?>/>
					<label for="add-device-activated">Activate device?</label>
				</div>
				<input type="submit" value="Add device" name="send_newdevice" class="raised" />
				<input type="reset" value="Clear form" />
			</form>
			<!--AJAX Request-->
			<script>
			setFormHandler('#form-add-device');
			</script>
			<?php
		break;
		case 'delete':
			if(isset($_GET['id']) && !empty($_GET['id'])) {

				$formID = intval($_GET['id']);
				
				// when confirmation was sent, delete
				if(isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
					
					// init database
					if(!(isset($SMAR_DB))) {
						$SMAR_DB = new SMAR_MysqlConnect();
					}
					
					// delete device
					$result = $SMAR_DB->dbquery("DELETE FROM ".SMAR_MYSQL_PREFIX."_device WHERE device_id = '".$SMAR_DB->real_escape_string($formID)."'");

					if($result === TRUE) {
						$SMAR_MESSAGES['success'][] = 'Device with ID "'.$formID.'" was successfully deleted.';
						$SMAR_MESSAGES['warning'][] = 'Please reload page to see changes in table.';
					} else {
						$SMAR_MESSAGES['error'][] = 'Deleting device with ID "'.$formID.'" failed.';
					}
					
				} else {
					$SMAR_MESSAGES['warning'][] = 'You are going to delete a device. When deleting a device, the device won\'t be able, to start the SMAR app.<br> Do you really want to delete the device with ID "'.$formID.'"?';
				}
				
				?>
				<div id="unitDeleteContainer">
				<h1>Delete device (ID: <?php echo $formID; ?>)</h1>
				<?php
				/* print messages */ if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
				if(!isset($_GET['confirm'])) {
					?>
					<form id="form-device-delete" method="get" data-target="#unitDeleteContainer" action="index.php?page=<?php echo urlencode($self.'?subpage=delete'); ?>">
						<input type="hidden" value="yes" name="confirm" />
						<input type="hidden" value="<?php echo $formID; ?>" name="id" />
						<input type="submit" value="Yes, delete device" name="send_delete" class="raised" />
					</form>
					<!--AJAX Request-->
					<script>
					setFormHandler('#form-device-delete');
					</script>
					<?php
				}
				echo '</div>';
				
			} else {
				$SMAR_MESSAGES['error'][] = 'No device ID was provided in URL parameters.';
				/* print messages */ if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
			}
		break;
		default:
			// form was sent
			if(isset($_POST['send_editdevice'])) {

				if(isset($_POST['edit-device-name']) && !empty($_POST['edit-device-name']) &&
					 isset($_POST['edit-device-hwaddress']) && !empty($_POST['edit-device-hwaddress']) &&
					 isset($_GET['editID']) && !empty($_GET['editID'])
					) {
						// init database
						if(!(isset($SMAR_DB))) {
							$SMAR_DB = new SMAR_MysqlConnect();
						}
						// get device data
						$resultCheck = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_device WHERE device_id = '".$SMAR_DB->real_escape_string($_GET['editID'])."'");
						if(!($rowCheck = $resultCheck->fetch_array())) {
							$SMAR_MESSAGES['error'][] = 'Device with ID '.$_GET['editID'].' not known.';
							unset($_GET['editID']);
						} else {
							$addName = strip_tags($_POST['edit-device-name']);
							$addHWADDRESS = strip_tags($_POST['edit-device-hwaddress']);
							if(isset($_POST['edit-device-activated'])) {
								$addActivated = 1;
							} else {
								$addActivated = 0;
							}

							// init database
							if(!(isset($SMAR_DB))) {
								$SMAR_DB = new SMAR_MysqlConnect();
							}

							$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_device
															SET device_name = '".$SMAR_DB->real_escape_string($addName)."',
															hwaddress = '".$SMAR_DB->real_escape_string($addHWADDRESS)."',
															activated = '".$SMAR_DB->real_escape_string($addActivated)."'
															WHERE device_id = '".$SMAR_DB->real_escape_string($_GET['editID'])."'");
							if($result === TRUE) {
								$SMAR_MESSAGES['success'][] = 'Device "'.$addName.'" was successfully updated.';
								unset($_GET['editID']);
							} else {
								$SMAR_MESSAGES['error'][] = 'Updating the device "'.$addName.'" failed.';
							}
						}
				} else {
					$addFailed = true;
					$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
				}
			}
			?>
			<h1>Device manager</h1>
			<?php
			if(isset($_GET['editID']) && !empty($_GET['editID'])) {
				// init database
				if(!(isset($SMAR_DB))) {
					$SMAR_DB = new SMAR_MysqlConnect();
				}

				// get shelf data
				$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_device WHERE device_id = '".$SMAR_DB->real_escape_string($_GET['editID'])."'");
				if(!($row = $result->fetch_array())) {
					$SMAR_MESSAGES['error'][] = 'Device with ID '.$_GET['editID'].' not known.';
					unset($_GET['editID']);
				}
			}
			// print messages
			if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
			if(isset($_GET['editID']) && !empty($_GET['editID'])) {
				?>
				<h2>Edit device: <?php echo($row['device_name']); ?></h2>
				<form id="form-edit-device" method="post" action="index.php?page=<?php echo urlencode($self.'?editID='.$row['device_id']); ?>">
					<div class="form-box swap-order">
						<input id="edit-device-name" type="text" name="edit-device-name" placeholder="Android Device" value="<?php echo(smar_form_input($row['device_name'])); ?>" />
						<label for="edit-device-name">Name</label>
					</div>
					<div class="form-box swap-order">
						<input id="edit-device-hwaddress" type="text" name="edit-device-hwaddress" placeholder="00:00:00:00:00:00" value="<?php echo(smar_form_input($row['hwaddress'])); ?>" />
						<label for="edit-device-hwaddress">Hardware Address (MAC Address)</label>
					</div>
					<div class="form-box swap-order">
						<input id="edit-device-activated" type="checkbox" name="edit-device-activated" <?php if($row['activated']) echo("checked"); ?>/>
						<label for="edit-device-activated">Activate device</label>
					</div>
					<input type="submit" value="Update device" name="send_editdevice" class="raised" />
				</form>
				<!--AJAX Request-->
				<script>
				setFormHandler('#form-edit-device');
				</script>
				<?php
			} else {
			?>
			<table>
				<thead>
				<tr>
					<th>ID</th>
					<th>Device Name</th>
					<th>Hardware Address (MAC Address)</th>
					<th>Activated</th>
					<th>Created</th>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				if(!(isset($SMAR_DB))) {
					$SMAR_DB = new SMAR_MysqlConnect();
				}
				$result = $SMAR_DB->dbquery("SELECT * FROM smar_device");
				if($result->num_rows === 0)
					echo '<tr><td colspan="5">No devices found</td></tr>';
				while($row = $result->fetch_array()) {
					echo "<tr><td>".$row['device_id']."</td>";
					echo "<td>".$row['device_name']."</td>";
					echo "<td>".$row['hwaddress']."</td>";
					echo "<td>".smar_parse_role_device($row['activated'])."</td>";
					echo "<td>".$row['created']."</td>";
					?>
					<td>
						<a href="<?php echo($self); ?>?editID=<?php echo($row['device_id']); ?>" class="ajax" title="Edit"><i class="mdi mdi-pencil"></i></a> <a href="<?php echo($self); ?>?subpage=delete&id=<?php echo($row['device_id']); ?>" title="Delete" class="link-deletedevice"><i class="mdi mdi-delete"></i></a>
					</td></tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<script>
			$('.link-deletedevice').on('click', function(e) {

				e.preventDefault();
				$target = $(e.delegateTarget);
				$.colorbox({
					href: $target.attr('href')+'&smar_include=true',
					closeButton: false,
					width: '80%',
					maxWidth: '700px'
				});
			});
			</script>
			<?php
			}
	}
	?>
</div>
<?php
}
?>
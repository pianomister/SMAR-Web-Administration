<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
* users.php                         *
*                                   *
************************************/

// extract file name
//$self = explode('/', $_SERVER['SCRIPT_NAME']);
//$self = $self[count($self)-1];//TODO
$self = 'users.php';
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

// include subnav if requested
if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true') {
	
	?>
	<nav id="nav-page">
		<ul>
			<li><a href="<?php echo $self; ?>" <?php echo ($subpage == '') ? 'class="smar-active"' : ''; ?>>Change my password</a></li>
			<li><a href="<?php echo $self.'?subpage=edit'; ?>" <?php echo ($subpage == 'edit') ? 'class="smar-active"' : ''; ?>>Edit a user</a></li>
			<li><a href="<?php echo $self.'?subpage=new'; ?>" <?php echo ($subpage == 'new') ? 'class="smar-active"' : ''; ?>>Add new user</a></li>
		</ul>
	</nav>
	<div id="smar-content-inner">
	<?php
}

// print messages
	if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }

	// page content
	switch($subpage) {
		
		case 'new':
			// form was sent
			if(isset($_POST['send_newuser'])) {

				if(isset($_POST['add-user-pnr']) && !empty($_POST['add-user-pnr']) &&
					 isset($_POST['add-user-name']) && !empty($_POST['add-user-name']) &&
					 isset($_POST['add-user-username']) && !empty($_POST['add-user-username']) &&
					 isset($_POST['add-user-password']) && !empty($_POST['add-user-password'])
					) {
						if(strlen($_POST['add-user-password']) > 5) { 
						
							if(strpos($_POST['add-user-name'], " ") === false) {
								$addFailed = true;
								$SMAR_MESSAGES['error'][] = 'Name field must have surname and lastname.';
							} else {

								$addPnr = strip_tags($_POST['add-user-pnr']);
								$addName = explode(" ", strip_tags($_POST['add-user-name']));
								$addSurname = "";
								for($i = 0; $i < count($addName)-1; $i++) {
									if($i != 0) $addSurname = $addSurname." ";
									$addSurname = $addSurname.$addName[$i];
								}
								$addLastname = $addName[count($addName)-1];
								$addUsername = strip_tags($_POST['add-user-username']);
								$addSalt = hash("sha256", substr($addUsername,0,2).time().substr($addLastname,0,2));
								$addPassword = hash("sha256", strip_tags($_POST['add-user-password']).$addSalt);
								$addRoleWeb = intval(strip_tags($_POST['add-user-role_web']));
								if(isset($_POST['add-user-role_device'])) {
									$addRoleDevice = 1;
								} else {
									$addRoleDevice = 0;
								}

								// init database
								if(!(isset($SMAR_DB))) {
									$SMAR_DB = new SMAR_MysqlConnect();
								}

								// get shelf data
								$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_user
																							(pnr, surname, lastname, username, role_web, role_device, password, salt, created) VALUES
																							('".$SMAR_DB->real_escape_string($addPnr)."', '".$SMAR_DB->real_escape_string($addSurname)."', '".$SMAR_DB->real_escape_string($addLastname)."', '".$SMAR_DB->real_escape_string($addUsername)."', '".$SMAR_DB->real_escape_string($addRoleWeb)."', '".$SMAR_DB->real_escape_string($addRoleDevice)."', '".$SMAR_DB->real_escape_string($addPassword)."', '".$SMAR_DB->real_escape_string($addSalt)."', NOW())");
								if($result === TRUE) {
									$SMAR_MESSAGES['success'][] = 'User "'.$addUsername.'" was successfully created.';
								} else {
									$addFailed = true;
									$SMAR_MESSAGES['error'][] = 'Inserting the user "'.$addUsername.'" into database failed.';
								}
							}
						} else {
							$addFailed = true;
							$SMAR_MESSAGES['error'][] = 'Password must be at least 6 characters long.';
						}
				} else {
					$addFailed = true;
					$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
				}
			}
			?>
			<h1>Add new user</h1>
			<?php
			// print messages
			if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
			?>
			<p>* All form fields are required.</p>
			<form id="form-add-user" method="post" action="index.php?page=<?php echo urlencode($self.'?subpage=new'); ?>">
				<div class="form-box swap-order">
					<input id="add-user-pnr" type="text" name="add-user-pnr" placeholder="abc123456" <?php if(isset($addFailed) && !empty($_POST['add-user-pnr'])) echo("value=\"".smar_form_input($_POST['add-user-pnr'])."\""); ?>/>
					<label for="add-user-pnr">Personnell Number</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-user-name" type="text" name="add-user-name" placeholder="Surname Lastname" <?php if(isset($addFailed) && !empty($_POST['add-user-name'])) echo("value=\"".smar_form_input($_POST['add-user-name'])."\""); ?>/>
					<label for="add-user-name">Name</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-user-username" type="text" name="add-user-username" placeholder="Username" <?php if(isset($addFailed) && !empty($_POST['add-user-username'])) echo("value=\"".smar_form_input($_POST['add-user-username'])."\""); ?>/>
					<label for="add-user-username">Username</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-user-password" type="password" name="add-user-password" placeholder="at least 6 characters" <?php if(isset($addFailed) && !empty($_POST['add-user-password'])) echo("value=\"".smar_form_input($_POST['add-user-password'])."\""); ?>/>
					<label for="add-user-password">Password</label>
				</div>
				<div class="form-box swap-order">
					<select id="add-user-role_web" name="add-user-role_web" size="1">
					<?php 
						$userRolesWebText = array("No Rights", "Read only", "Products & Units", "Products, Units, Shelves, Sections", "Edit all", "Manager", "Administrator");
						$userRolesWebValue = array(0,1,2,3,4,8,9);
						for($l = 0; $l < count($userRolesWebText); $l++) {
							echo("<option value=\"".$userRolesWebValue[$l]."\"");
							if(isset($addFailed) && !empty($_POST['add-user-role_web'])) 
								if(intval(strip_tags($_POST['add-user-role_web'])) == $userRolesWebValue[$l])
									echo(" selected");
							echo(">".$userRolesWebText[$l]."</option>");
						}
					?>
					  <!-- <option value="0">No Rights</option>
					  <option value="1">Read only</option>
					  <option value="2">Products & Units</option>
					  <option value="3">Products, Units, Shelves, Sections</option>
					  <option value="4">Edit all</option>
					  <option value="8">Manager</option>
					  <option value="9">Administrator</option> -->
					</select>
					<label for="add-user-role_web">Role @ Web Administration</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-user-role_device" type="checkbox" name="add-user-role_device"<?php if(isset($addFailed) && isset($_POST['add-user-role_device'])) echo("checked"); ?>/>
					<label for="add-user-role_device">Role @ Device</label>
				</div>
				<input type="submit" value="Add user" name="send_newuser" class="raised" />
				<input type="reset" value="Clear form" />
			</form>
			<!--AJAX Request-->
			<script>
			setFormHandler('#form-add-user');
			</script>
			<?php
			break;
		case 'edit':
			?>
			<h1>User Management</h1>
			<table>
				<thead>
				<tr>
					<th>ID</th>
					<th>personnell number</th>
					<th>Surname</th>
					<th>Last name</th>
					<th>Username</th>
					<th>Role</th>
					<th>Device</th>
					<th>Created</th>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				if(!(isset($SMAR_DB))) {
					$SMAR_DB = new SMAR_MysqlConnect();
				}
				$result = $SMAR_DB->dbquery("SELECT * FROM smar_user");
				while($row = $result->fetch_array()) {
					echo "<tr><td>".$row['user_id']."</td>";
					echo "<td>".$row['pnr']."</td>";
					echo "<td>".$row['surname']."</td>";
					echo "<td>".$row['lastname']."</td>";
					echo "<td>".$row['username']."</td>";
					echo "<td>".smar_parse_role_web($row['role_web'])."</td>";
					echo "<td>".smar_parse_role_device($row['role_device'])."</td>";
					echo "<td>".$row['created']."</td>";
					?>
					<td>
						<a href="#" title="Change password"><i class="mdi mdi-key-variant"></i></a> <a href="#" title="Edit"><i class="mdi mdi-pencil"></i></a> <a href="#" title="Delete"><i class="mdi mdi-delete"></i></a>
					</td></tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<?php
			break;
		case 'changepw':
		default:
			if(isset($_GET['editID']) && !empty($_GET['editID'])) {
				$userid = $_GET['editID'];
			} else {
				$userid = $_SESSION['loginID'];
			}
			
			$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_user WHERE user_id = '".$SMAR_DB->real_escape_string($userid)."'");
			if(!($row = $result->fetch_array())) {
				$SMAR_MESSAGES['error'][] = 'User with ID '.$userid.' not known.';
			}
			// form was sent
			if(isset($_POST['send_changepw'])) {

				if(isset($_POST['change-user-password-old']) && !empty($_POST['change-user-password-old']) &&
					 isset($_POST['change-user-password-new']) && !empty($_POST['change-user-password-new']) &&
					 isset($_POST['change-user-password-confirm']) && !empty($_POST['change-user-password-confirm'])
					) {
						if(strlen($_POST['change-user-password-new']) > 5) { 
						
							if($_POST['change-user-password-new'] == $_POST['change-user-password-confirm']) {
								//Abfrage der Logindaten
								// init database
								if(!(isset($SMAR_DB))) {
									$SMAR_DB = new SMAR_MysqlConnect();
								}
								
								$passwort = hash("sha256", $_POST['change-user-password-old'].$row['salt']);
								if($passwort == $row['password']) {
									$addSalt = hash("sha256", substr($row['username'],0,2).time().substr($row['lastname'],0,2));
									$addPassword = hash("sha256", strip_tags($_POST['change-user-password-new']).$addSalt);
									$resultUpdate = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_user SET password = '".$SMAR_DB->real_escape_string($addPassword)."', 
																		salt = '".$SMAR_DB->real_escape_string($addSalt)."' WHERE user_id = '".$SMAR_DB->real_escape_string($userid)."'");
									if($resultUpdate === TRUE) {
										$SMAR_MESSAGES['success'][] = 'Password sucessfully updated.';
									} else {
										$SMAR_MESSAGES['error'][] = 'Password update failed.';
									}
								} else {
									$SMAR_MESSAGES['error'][] = 'Old password incorrect.';
								}
							} else {
								$SMAR_MESSAGES['error'][] = 'New password and confirm new password must be equal.';
							}
						} else {
							$SMAR_MESSAGES['error'][] = 'Password must be at least 6 characters long.';
						}
				} else {
					$SMAR_MESSAGES['error'][] = 'Please fill in all fields.';
				}
			}
			?>
			<h1>Change my password</h1>
			<?php
			// print messages
			if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
			?>
			<h2>Web Administration</h2>
			<form id="form-change-pw" method="post" action="index.php?page=<?php echo urlencode($self.'?subpage=changepw'); ?>">
				<div class="form-box swap-order">
					<input id="change-user-password-old" type="password" name="change-user-password-old" placeholder="Old Password" />
					<label for="password">Old Password</label>
				</div>
				<div class="form-box swap-order">
					<input id="change-user-password-new" type="password" name="change-user-password-new" placeholder="at least 6 characters" />
					<label for="password">New Password</label>
				</div>
				<div class="form-box swap-order">
					<input id="change-user-password-confirm" type="password" name="change-user-password-confirm" placeholder="at least 6 characters" />
					<label for="password">Confirm New Password</label>
				</div>
				<input type="submit" name="send_changepw" value="Change password" />
			</form>
			<!--AJAX Request-->
			<script>
			setFormHandler('#form-change-pw');
			</script>
			<?php
			if($row['role_device'] == 1) {
			?>
				<h2>Device</h2>
				<a href="#">Print and activate new QR-Code for this user.</a>
			<?php
			}
	}

if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true')
	echo '</div>';
?>

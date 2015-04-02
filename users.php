<?php
/************************************
*									*
* SMAR								*
* by								*
* Raffael Wojtas					*
* Stephan Giesau					*
* Sebastian Kowalski				*
*									*
* users.php							*
*									*
************************************/

require_once('_functions/_functions.php');
require_once('inc_session_check.php');

// extract file name
$self = explode('/', $_SERVER['SCRIPT_NAME']);
$self = $self[count($self)-1];
$subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';

// check for type of call (direct/AJAX)
// on direct call, redirect to index for full layout
if(!isset($_GET['smar_include']) || $_GET['smar_include'] != 'true') {
	$url = $self;
	$url .= (strlen($_SERVER['QUERY_STRING']) != 0) ? '?'.$_SERVER['QUERY_STRING'] : '';
	header( 'location: index.php?page='.urlencode($url) );
}

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
	<?php
}
?>
<div id="smar-content-inner">
	<?php
	// print messages
	if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }

	// page content
	switch($subpage) {
		
		case 'new':
			?>
			<h1>Add new user</h1>
			<p>* All form fields are required.</p>
			<form>
				<div class="form-box swap-order">
					<input id="name" name="name" placeholder="abc123456" />
					<label for="name">Personnell Number</label>
				</div>
				<div class="form-box swap-order">
					<input id="name" name="name" placeholder="Surname Lastname" />
					<label for="name">Name</label>
				</div>
				<div class="form-box swap-order">
					<input id="username" name="username" placeholder="Username" />
					<label for="username">Username</label>
				</div>
				<div class="form-box swap-order">
					<input id="password" type="password" name="password" placeholder="mind. 16 Zeichen" />
					<label for="password">Password</label>
				</div>
				<div class="form-box swap-order">
					<select id="role_web" name="role_web" size="1">
					  <option value="0">No Rights</option>
					  <option value="1">Read only</option>
					  <option value="2">Products & Units</option>
					  <option value="3">Products, Units, Shelves, Sections</option>
					  <option value="4">Edit all</option>
					  <option value="8">Manager</option>
					  <option value="9">Administrator</option>
					</select>
					<label for="role_web">Role @ Web Administration</label>
				</div>
				<div class="form-box swap-order">
					<input id="role_device" type="checkbox" name="role_device"/>
					<label for="role_device">Role @ Device</label>
				</div>
				<input type="submit" value="Add user" />
			</form>
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
						<a href="#"><i class="mdi mdi-dots-horizontal"></i></a> <a href="#"><i class="mdi mdi-pencil"></i></a> <a href="#"><i class="mdi mdi-delete"></i></a>
					</td></tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<?php
			break;
		default:
			?>
			<h1>Change my password</h1>
			<h2>Web Administration</h2>
			<form>
				<div class="form-box swap-order">
					<input id="password" type="password" name="password" placeholder="mind. 16 Zeichen" />
					<label for="password">Old Password</label>
				</div>
				<div class="form-box swap-order">
					<input id="password" type="password" name="password" placeholder="mind. 16 Zeichen" />
					<label for="password">New Password</label>
				</div>
				<div class="form-box swap-order">
					<input id="password" type="password" name="password" placeholder="mind. 16 Zeichen" />
					<label for="password">Confirm New Password</label>
				</div>
				<input type="submit" value="Change password" />
			</form>
			<h2>Device</h2>
			<a href="#">Print and activate new QR-Code for this user.</a>
			<?php
	}
	?>
</div>
<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
* shelves.php                       *
*                                   *
************************************/

// extract file name
//$self = explode('/', $_SERVER['SCRIPT_NAME']);
//$self = (strpos($_SERVER['QUERY_STRING'], 'page=') >= 0) ? explode('?', urldecode(explode('page=', ' '.$_SERVER['QUERY_STRING'])[1]))[0] : $self[count($self)-1];
$self = 'shelves.php';//TODO
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
			<li><a href="<?php echo $self; ?>" <?php echo ($subpage == '') ? 'class="smar-active"' : ''; ?>>Shelves</a></li>
			<li><a href="<?php echo $self.'?subpage=addshelf'; ?>" <?php echo ($subpage == 'addshelf') ? 'class="smar-active"' : ''; ?>>Add shelf</a></li>
		</ul>
	</nav>
	<div id="smar-content-inner">
	<?php
}

// print messages
if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }

// page content
switch($subpage) {

	case 'designer':
	
		if(isset($_GET['id']) && !empty($_GET['id'])) {
			
			$formID = intval($_GET['id']);
			
			// init database
			if(!(isset($SMAR_DB))) {
				$SMAR_DB = new SMAR_MysqlConnect();
			}

			$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_shelf WHERE shelf_id = '".$SMAR_DB->real_escape_string($formID)."' LIMIT 1");
			if($result->num_rows == 1) {
					$row = $result->fetch_array(MYSQLI_ASSOC);
				
				echo '<h1>Shelf Designer</h1>
							<h2>'.$row['name'].' (ID: '.$row['shelf_id'].')</h2>
							<p>
								<a href="" title="Add section"><i class="bg-icon mdi mdi-plus"></i> Add new section</a> &nbsp;&nbsp;
							</p>
							<!--<div id="designer-canvas" style="background-color: #ccc;width: '.$row['size_x'].'px; height: '.$row['size_y'].'px;"></div>-->';
				
				echo file_get_contents(SMAR_SITE_URL.SMAR_CURRENT_DIR.'svg_generator.php?id='.$formID);
				
				echo '<h2>Sections</h2>';
				
				// get sections
				$result = $SMAR_DB->dbquery("SELECT s.section_id, s.name, s.capacity, p.name as product FROM ".SMAR_MYSQL_PREFIX."_section s, ".SMAR_MYSQL_PREFIX."_product p WHERE shelf_id = '".$SMAR_DB->real_escape_string($formID)."' AND p.product_id = s.product_id");
				if($result->num_rows != 0) {
						echo '<table>
									<thead>
									<tr>
										<th>ID</th>
										<th>Name</th>
										<th>Capacity</th>
										<th>Product</th>
										<th>Actions</th>
									</tr>
									</thead>
									<tbody>';
						while($row = $result->fetch_array(MYSQLI_ASSOC)) {
							echo '<tr>
											<td>'.$row['section_id'].'</td>
											<td>'.$row['name'].'</td>
											<td>'.$row['capacity'].'</td>
											<td>'.$row['product'].'</td>
											<td>...</td>
										</tr>';
						}
					echo '</tbody></table>';
				} else {
					echo '<p>No sections found for this shelf.</p>';
				}
			
			// id not found
			} else {
					$SMAR_MESSAGES['error'][] = 'No item was found for given ID '.smar_form_input($formID).'.';
			}
		// id not given in parameter
		} else {
			$SMAR_MESSAGES['error'][] = 'No item ID was provided in URL parameters.';
		}
	
		// print messages
		if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
	
		break;
	case 'editshelf':

		// set action type
		$page_action = 'edit';

		if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {

			$formID = intval($_REQUEST['id']);

			// edit form was sent
			if(isset($_POST['send_editshelf'])) {

				if(isset($_POST['form-shelf-name']) && 
					 isset($_POST['form-shelf-barcode']) &&
					 isset($_POST['form-shelf-x']) &&
					 isset($_POST['form-shelf-y']) &&
					 isset($_POST['form-shelf-z'])
					) {

					$formName = strip_tags($_POST['form-shelf-name']);
					$formBarcode = intval(strip_tags($_POST['form-shelf-barcode']));	
					$formX = intval(strip_tags($_POST['form-shelf-x']));
					$formY = intval(strip_tags($_POST['form-shelf-y']));
					$formZ = intval(strip_tags($_POST['form-shelf-z']));

					if(!empty($_POST['form-shelf-name']) &&
						 !empty($_POST['form-shelf-x']) &&
						 !empty($_POST['form-shelf-y']) &&
						 !empty($_POST['form-shelf-z']) &&
						 !empty($_POST['form-shelf-barcode'])
						) {

						// init database
						if(!(isset($SMAR_DB))) {
							$SMAR_DB = new SMAR_MysqlConnect();
						}

						// get shelf data
						$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_shelf SET
																					name = '".$SMAR_DB->real_escape_string($formName)."',
																					size_x = '".$SMAR_DB->real_escape_string($formX)."',
																					size_y = '".$SMAR_DB->real_escape_string($formY)."',
																					barcode = '".$SMAR_DB->real_escape_string($formBarcode)."',
																					size_z = '".$SMAR_DB->real_escape_string($formZ)."'
																					WHERE shelf_id = '".$SMAR_DB->real_escape_string($formID)."'");
						if($result === TRUE) {
							$SMAR_MESSAGES['success'][] = 'Changes for "'.$formName.'" were successfully saved.';
						} else {
							$SMAR_MESSAGES['error'][] = 'Inserting the changes for shelf "'.$formName.'" into database failed.';
						}
					} else {
						$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
					}
				}

			// get initial contents
			} else {

				// init database
				if(!(isset($SMAR_DB))) {
					$SMAR_DB = new SMAR_MysqlConnect();
				}

				$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_shelf WHERE shelf_id = '".$SMAR_DB->real_escape_string($formID)."' LIMIT 1");
				if($result->num_rows == 1) {
					$row = $result->fetch_array(MYSQLI_ASSOC);

					$formName = $row['name'];
					$formBarcode = $row['barcode'];
					$formX = $row['size_x'];
					$formY = $row['size_y'];
					$formZ = $row['size_z'];
					$formCreated = $row['created'];
					$formLastupdate = $row['lastupdate'];

				} else {
					$SMAR_MESSAGES['error'][] = 'No item was found for given ID '.smar_form_input($formID).'.';
				}
			}

		} else {
			$SMAR_MESSAGES['error'][] = 'No item ID was provided in URL parameters.';
		}

		?>
		<h1>Edit shelf</h1>
		<?php
	case 'addshelf':

		// set action type
		if(!isset($page_action))
			$page_action = 'add';

		// form was sent
		if($page_action == 'add' && isset($_POST['send_addshelf'])) {

			if(isset($_POST['form-shelf-name']) &&
					 isset($_POST['form-shelf-x']) &&
					 isset($_POST['form-shelf-y']) && 
					 isset($_POST['form-shelf-barcode']) &&
					 isset($_POST['form-shelf-z'])
					) {

					$formName = strip_tags($_POST['form-shelf-name']);
					$formBarcode = intval(strip_tags($_POST['form-shelf-barcode']));	
					$formX = intval(strip_tags($_POST['form-shelf-x']));
					$formY = intval(strip_tags($_POST['form-shelf-y']));
					$formZ = intval(strip_tags($_POST['form-shelf-z']));

					if(!empty($_POST['form-shelf-name']) &&
						 !empty($_POST['form-shelf-x']) &&
						 !empty($_POST['form-shelf-y']) &&
						 !empty($_POST['form-shelf-z']) &&
						 !empty($_POST['form-shelf-barcode'])
						) {

					// init database
					if(!(isset($SMAR_DB))) {
						$SMAR_DB = new SMAR_MysqlConnect();
					}

					// get shelf data
					$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_shelf
																				(name, size_x, size_y, barcode, size_z, created) VALUES
																				('".$SMAR_DB->real_escape_string($formName)."', '".$SMAR_DB->real_escape_string($formX)."', '".$SMAR_DB->real_escape_string($formY)."', '".$SMAR_DB->real_escape_string($formBarcode)."', '".$SMAR_DB->real_escape_string($formZ)."', NOW())");
					if($result === TRUE) {
						$SMAR_MESSAGES['success'][] = 'Shelf "'.$formName.'" was successfully created.';
					} else {
						$SMAR_MESSAGES['error'][] = 'Inserting the shelf "'.$formName.'" into database failed.';
					}
				} else {
					$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
				}
			}
		}

		if($page_action == 'add')
			echo '<h1>Add shelf</h1>';

		// print messages
		if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
		?>
		<form id="form-shelf" method="post" action="index.php?page=<?php echo urlencode($self.'?subpage='.$page_action.'shelf'); ?>">
			<div class="form-box swap-order">
				<input id="form-shelf-name" type="text" name="form-shelf-name" placeholder="Title or short description" value="<?php if(isset($formName)) echo smar_form_input($formName); ?>" />
				<label for="form-shelf-name">Shelf name</label>
			</div>
			<div class="form-box swap-order">
				<input id="form-shelf-x" type="number" name="form-shelf-x" placeholder="0 cm" value="<?php if(isset($formX)) echo smar_form_input($formX); ?>" />
				<label for="form-shelf-x">Width (cm)</label>
			</div>
			<div class="form-box swap-order">
				<input id="form-shelf-y" type="number" name="form-shelf-y" placeholder="0 cm" value="<?php if(isset($formY)) echo smar_form_input($formY); ?>" />
				<label for="form-shelf-y">Height (cm)</label>
			</div>
			<div class="form-box swap-order">
				<input id="form-shelf-z" type="number" name="form-shelf-z" placeholder="0 cm" value="<?php if(isset($formZ)) echo smar_form_input($formZ); ?>" />
				<label for="form-shelf-z">Depth (cm)</label>
			</div>
			<div class="form-box swap-order">
				<input id="form-shelf-barcode" type="text" name="form-shelf-barcode" placeholder="0123456789" value="<?php if(isset($formBarcode)) echo smar_form_input($formBarcode); ?>" />
				<label for="form-shelf-barcode">Barcode</label>
			</div>
			<?php
			if($page_action == 'add') {
				echo '<input type="submit" value="Add shelf" name="send_addshelf" class="raised" />';
			} else {
				echo '<input type="hidden" value="'.$formID.'" name="id" />';
				?>
				<div class="form-box">
					<span class="label">Date created</label>
					<span class="input"><?php echo isset($formCreated) ? smar_form_input($formCreated) : '&mdash;'; ?></label>
				</div>
				<div class="form-box">
						<span class="label">Last update</label>
						<span class="input"><?php echo isset($formLastupdate) ? smar_form_input($formLastupdate) : '&mdash;'; ?></label>
				</div>
				<input type="submit" value="Save changes" name="send_editshelf" class="raised" />
				<?php
			}
			?>
			<input type="reset" value="Reset form" name="reset" />
		</form>
		<!--AJAX Request-->
		<script>
		setFormHandler('#form-shelf');
		</script>
		<?php
		break;
	default:
		?>
		<div class="flex">
			<h1>Shelves</h1>
			<div>
				<?php
				// init database
				if(!(isset($SMAR_DB))) {
					$SMAR_DB = new SMAR_MysqlConnect();
				}

				$filter = '';
				$formFilter = '';
				if(isset($_GET['filter']) && !empty($_GET['filter'])) {
					$formFilter = $_GET['filter'];
					$filter = " WHERE name LIKE '%".$SMAR_DB->real_escape_string($formFilter)."%'";
				}
				?>
				<form id="form-filter" method="get" action="index.php?page=<?php echo urlencode($self); ?>">
					<input type="hidden" name="page" value="<?php echo urlencode($self); ?>">
					<input type="text" name="filter" placeholder="Filter by name" value="<?php if(isset($formFilter) && !empty($formFilter)) echo smar_form_input($formFilter); ?>" class="input-medium">
				</form>
				<?php
				// pagination
				$items_per_page = 20;	
				$current_page = 0;
				if(isset($_GET['limit']) && !empty($_GET['limit']))
					$current_page = intval($_GET['limit']);
				$limit = $items_per_page * $current_page;

				$result = $SMAR_DB->dbquery("SELECT count(*) as items FROM ".SMAR_MYSQL_PREFIX."_shelf".$filter);
				$num_items = $result->fetch_array(MYSQLI_ASSOC)['items'];

				echo smar_pagination($self.'?page=shelves.php&filter='.$formFilter, $num_items, $items_per_page, $current_page);
				?>
			</div>
		</div>
		<table>
			<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Width</th>
				<th>Height</th>
				<th>Depth</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
				<?php
				// get shelf
				$result = $SMAR_DB->dbquery("SELECT shelf_id, name, size_x, size_y, size_z FROM ".SMAR_MYSQL_PREFIX."_shelf".$filter." ORDER BY shelf_id LIMIT ".$SMAR_DB->real_escape_string($limit).",".$SMAR_DB->real_escape_string($items_per_page));
				if($result->num_rows > 0) {
					while($row = $result->fetch_array(MYSQLI_ASSOC)) {
						echo '<tr>
							<td>'.$row['shelf_id'].'</td>
							<td>'.$row['name'].'</td>
							<td>'.$row['size_x'].'</td>
							<td>'.$row['size_y'].'</td>
							<td>'.$row['size_z'].'</td>
							<td>
								<a href="'.$self.'?subpage=editshelf&id='.$row['shelf_id'].'" title="Edit" class="ajax"><i class="mdi mdi-pencil"></i></a>
								<a href="'.$self.'?subpage=designer&id='.$row['shelf_id'].'" title="Shelf Designer" class="ajax"><i class="mdi mdi-math-compass"></i></a>
								<!--<a href="'.$self.'?subpage=deleteshelf&id='.$row['shelf_id'].'" title="Delete" class="ajax"><i class="mdi mdi-delete"></i></a>-->
							</td>
						</tr>';
					}
				} else {
					echo '<tr><td colspan="5">No shelves found</td></tr>';
				}
				?>
			</tbody>
		</table>
		<!--AJAX Request-->
		<script>
		setFormHandler('#form-filter');
		</script>
		<?php
}

if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true')
	echo '</div>';
?>
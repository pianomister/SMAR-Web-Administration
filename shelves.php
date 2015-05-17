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

if($_SESSION['loginRole'] < 1) {
	$SMAR_MESSAGES['error'][] = 'Insufficient permissions for shelves management';
	smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES);
} else {

// include subnav if requested
if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true') {
	
	?>
	<nav id="nav-page">
		<ul>
			<li><a href="<?php echo $self; ?>" <?php echo ($subpage == '') ? 'class="smar-active"' : ''; ?>>Shelves</a></li>
			<?php if($_SESSION['loginRole'] >= 2) { ?><li><a href="<?php echo $self.'?subpage=addshelf'; ?>" <?php echo ($subpage == 'addshelf') ? 'class="smar-active"' : ''; ?>>Add shelf</a></li><?php } ?>
		</ul>
	</nav>
	<div id="smar-content-inner">
	<?php
}

// print messages
if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }

// page content
switch($subpage) {

case 'editsection':

		// set action type
		$page_action = 'edit';

		if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {

			$formID = intval($_REQUEST['id']);

			// edit form was sent
			if(isset($_POST['send_editsection'])) {

				if(isset($_POST['form-section-name']) && 
					 isset($_POST['form-section-productid']) &&
					 isset($_POST['form-section-x']) &&
					 isset($_POST['form-section-y']) &&
					 isset($_POST['form-section-posx']) &&
					 isset($_POST['form-section-posy']) &&
					 isset($_POST['form-section-capacity']) &&
					 isset($_POST['form-section-mincapacity'])
					) {

					$formName = strip_tags($_POST['form-section-name']);
					$formProductID = intval(strip_tags($_POST['form-section-productid']));	
					$formX = intval(strip_tags($_POST['form-section-x']));
					$formY = intval(strip_tags($_POST['form-section-y']));
					$formPosX = intval(strip_tags($_POST['form-section-posx']));
					$formPosY = intval(strip_tags($_POST['form-section-posy']));
				  $formCapacity = intval(strip_tags($_POST['form-section-capacity']));
					$formMinCapacity = intval(strip_tags($_POST['form-section-mincapacity']));

					if(!empty($_POST['form-section-name']) &&
						 !empty($_POST['form-section-productid']) &&
						 !empty($_POST['form-section-x']) &&
						 !empty($_POST['form-section-y']) &&
						 !empty($_POST['form-section-capacity']) &&
						 !empty($_POST['form-section-mincapacity'])
						) {

						// init database
						if(!(isset($SMAR_DB))) {
							$SMAR_DB = new SMAR_MysqlConnect();
						}

						// get section data
						$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_section SET
																					name = '".$SMAR_DB->real_escape_string($formName)."',
																					size_x = '".$SMAR_DB->real_escape_string($formX)."',
																					size_y = '".$SMAR_DB->real_escape_string($formY)."',
																					product_id = '".$SMAR_DB->real_escape_string($formProductID)."',
																					position_x = '".$SMAR_DB->real_escape_string($formPosX)."',
																					position_y = '".$SMAR_DB->real_escape_string($formPosY)."',
																					capacity = '".$SMAR_DB->real_escape_string($formCapacity)."',
																					min_capacity = '".$SMAR_DB->real_escape_string($formMinCapacity)."'
																					WHERE section_id = '".$SMAR_DB->real_escape_string($formID)."'");
						if($result === TRUE) {
							$SMAR_MESSAGES['success'][] = 'Changes for "'.$formName.'" were successfully saved.';
							
							$result = $SMAR_DB->dbquery("SELECT shelf_id FROM ".SMAR_MYSQL_PREFIX."_section WHERE section_id = ''");
							if($result->num_rows != 0) {
								$row = $result->fetch_array(MYSQLI_ASSOC);
								smar_update_shelf_svg($row['shelf_id']);
							}
						} else {
							$SMAR_MESSAGES['error'][] = 'Inserting the changes for section "'.$formName.'" into database failed.';
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

				$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_section WHERE section_id = '".$SMAR_DB->real_escape_string($formID)."' LIMIT 1");
				if($result->num_rows == 1) {
					$row = $result->fetch_array(MYSQLI_ASSOC);

					$formName = $row['name'];
					$formProductID = $row['product_id'];
					$formX = $row['size_x'];
					$formY = $row['size_y'];
					$formPosX = $row['position_x'];
					$formPosY = $row['position_y'];
					$formCapacity = $row['capacity'];
					$formMinCapacity = $row['min_capacity'];
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
		<div id="formSectionContainer"><h1>Edit section</h1>
		<?php
	case 'addsection':
		if($_SESSION['loginRole'] >= 2) {
		// set action type
		if(!isset($page_action))
			$page_action = 'add';
	
		if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
			
			$formID = intval($_REQUEST['id']);
	
			// form was sent
			if($page_action == 'add' && isset($_POST['send_addsection'])) {

				if(isset($_POST['form-section-name']) && 
					 isset($_POST['form-section-productid']) &&
					 isset($_POST['form-section-x']) &&
					 isset($_POST['form-section-y']) &&
					 isset($_POST['form-section-posx']) &&
					 isset($_POST['form-section-posy']) &&
					 isset($_POST['form-section-capacity']) &&
					 isset($_POST['form-section-mincapacity'])
					) {

					$formName = strip_tags($_POST['form-section-name']);
					$formProductID = intval(strip_tags($_POST['form-section-productid']));	
					$formX = intval(strip_tags($_POST['form-section-x']));
					$formY = intval(strip_tags($_POST['form-section-y']));
					$formPosX = intval(strip_tags($_POST['form-section-posx']));
					$formPosY = intval(strip_tags($_POST['form-section-posy']));
					$formCapacity = intval(strip_tags($_POST['form-section-capacity']));
					$formMinCapacity = intval(strip_tags($_POST['form-section-mincapacity']));

					if(!empty($_POST['form-section-name']) &&
						 !empty($_POST['form-section-productid']) &&
						 !empty($_POST['form-section-x']) &&
						 !empty($_POST['form-section-y']) &&
						 !empty($_POST['form-section-capacity']) &&
						 !empty($_POST['form-section-mincapacity'])
						) {

						// init database
						if(!(isset($SMAR_DB))) {
							$SMAR_DB = new SMAR_MysqlConnect();
						}

						// insert section data
						$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_section
																					(shelf_id, name, size_x, size_y, product_id, position_x, position_y, capacity, min_capacity, created) VALUES
																					('".$SMAR_DB->real_escape_string($formID)."', '".$SMAR_DB->real_escape_string($formName)."', '".$SMAR_DB->real_escape_string($formX)."', '".$SMAR_DB->real_escape_string($formY)."', '".$SMAR_DB->real_escape_string($formProductID)."', '".$SMAR_DB->real_escape_string($formPosX)."', '".$SMAR_DB->real_escape_string($formPosY)."', '".$SMAR_DB->real_escape_string($formCapacity)."', '".$SMAR_DB->real_escape_string($formMinCapacity)."', NOW())");
						if($result === TRUE) {
							$SMAR_MESSAGES['success'][] = 'Section "'.$formName.'" was successfully created.';
							smar_update_shelf_svg($formID);
						} else {
							$SMAR_MESSAGES['error'][] = 'Inserting the section "'.$formName.'" into database failed.';
						}
					} else {
						$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
					}
				}
			}
		} else {
			$SMAR_MESSAGES['error'][] = 'No ID was given in parameters.';
		}

		if($page_action == 'add')
			echo '<div id="formSectionContainer"><h1>Add section</h1>';

		// print messages
		if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
	
		if(isset($formID)) {
			?>
			<form id="form-section" method="post" data-target="#formSectionContainer" action="index.php?page=<?php echo urlencode($self.'?subpage='.$page_action.'section'); ?>">
				<div class="form-box">
					<span class="label">Shelf ID</span>
					<span class="input"><?php echo $formID; ?></span>
				</div>
				<div class="form-box swap-order">
					<input id="form-section-name" type="text" name="form-section-name" placeholder="Title or short description" value="<?php if(isset($formName)) echo smar_form_input($formName); ?>" />
					<label for="form-section-name">Section name</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-section-capacity" type="number" name="form-section-capacity" placeholder="Capacity" value="<?php if(isset($formCapacity)) echo smar_form_input($formCapacity); ?>" />
					<label for="form-section-capacity">Capacity</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-section-mincapacity" type="number" name="form-section-mincapacity" placeholder="Minimum capacity" value="<?php if(isset($formMinCapacity)) echo smar_form_input($formMinCapacity); ?>" />
					<label for="form-section-mincapacity">Minimum capacity (for alerts)</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-section-x" type="number" name="form-section-x" placeholder="0 cm" value="<?php if(isset($formX)) echo smar_form_input($formX); ?>" />
					<label for="form-section-x">Width (cm)</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-section-y" type="number" name="form-section-y" placeholder="0 cm" value="<?php if(isset($formY)) echo smar_form_input($formY); ?>" />
					<label for="form-section-y">Height (cm)</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-section-posx" type="number" name="form-section-posx" placeholder="0 cm" value="<?php if(isset($formPosX)) echo smar_form_input($formPosX); ?>" />
					<label for="form-section-posx">Horizontal (x) position (cm)</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-section-posy" type="number" name="form-section-posy" placeholder="0 cm" value="<?php if(isset($formPosY)) echo smar_form_input($formPosY); ?>" />
					<label for="form-section-posy">Vertical (y) position (cm)</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-section-productid" type="hidden" name="form-section-productid" value="<?php if(isset($formProductID)) echo smar_form_input($formProductID); ?>">
					<input id="form-section-product" type="text" name="form-section-product" placeholder="Type to search for product / article nr." value="<?php if(isset($formProductID)) echo smar_form_input($formProductID); ?>" />
					<label for="form-section-product">Product (ID)</label>
				</div>
				<?php
				echo '<input type="hidden" value="'.$formID.'" name="id" />';
				if($page_action == 'add') {
					echo '<input type="submit" value="Add section" name="send_addsection" class="raised" />';
				} else {
					?>
					<div class="form-box">
						<span class="label">Date created</label>
						<span class="input"><?php echo isset($formCreated) ? smar_form_input($formCreated) : '&mdash;'; ?></label>
					</div>
					<div class="form-box">
							<span class="label">Last update</label>
							<span class="input"><?php echo isset($formLastupdate) ? smar_form_input($formLastupdate) : '&mdash;'; ?></label>
					</div>
					<input type="submit" value="Save changes" name="send_editsection" class="raised" />
					<?php
				}
				?>
				<input type="reset" value="Reset form" name="reset" />
			</form>
			<!--AJAX Request-->
			<script>
			setFormHandler('#form-section');
			setAutocompleteHandler('#form-section-product', 'product', '#form-section-productid');
			</script>
			</div>
			<?php
		}
		} else {
			$SMAR_MESSAGES['error'][] = 'Insufficient permissions for shelves management';
			smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES);
		}
		break;
	case 'designer':
		if($_SESSION['loginRole'] >= 2) {
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
								<a href="'.$self.'?subpage=addsection&amp;id='.$formID.'" id="link-addsection"><i class="bg-icon mdi mdi-plus"></i> Add new section</a> &nbsp;&nbsp;
								<a href="#" id="link-designer-save"><i class="bg-icon mdi mdi-content-save"></i> Save changes</a> &nbsp;&nbsp;
								<a href="'.$self.'?subpage=designer&amp;id='.$formID.'" id="link-refresh" class="ajax"><i class="bg-icon mdi mdi-refresh"></i> Refresh view</a>
							</p>
							<div id="designer-canvas" data-shelfid="'.$formID.'" style="width: '.$row['size_x'].'px; height: '.$row['size_y'].'px;">';

				// get sections
				$sectionsTable = '';
				$result = $SMAR_DB->dbquery("SELECT s.section_id, s.name, s.capacity, s.size_x, s.size_y, s.position_x, s.position_y, p.name as product FROM ".SMAR_MYSQL_PREFIX."_section s, ".SMAR_MYSQL_PREFIX."_product p WHERE shelf_id = '".$SMAR_DB->real_escape_string($formID)."' AND p.product_id = s.product_id ORDER BY position_x");

				if($result->num_rows != 0) {
					$sectionsTable .= '<table>
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
						
						// create element on canvas
						echo '<div class="canvas-section" style="transform: translate('.$row['position_x'].'px, '.$row['position_y'].'px);width:'.$row['size_x'].'px;height:'.$row['size_y'].'px;" data-sectionid="'.$row['section_id'].'" data-sizex="'.$row['size_x'].'" data-sizey="'.$row['size_y'].'" data-x="'.$row['position_x'].'" data-y="'.$row['position_y'].'">'.$row['section_id'].'</div>';
						
						// append to sections table
						$sectionsTable .= '<tr>
										<td>'.$row['section_id'].'</td>
										<td>'.$row['name'].'</td>
										<td>'.$row['capacity'].'</td>
										<td>'.$row['product'].'</td>
										<td>';
						if($_SESSION['loginRole'] >= 2) {
							$sectionsTable .= '<a href="'.$self.'?subpage=editsection&id='.$row['section_id'].'" title="Edit" class="link-editsection"><i class="mdi mdi-pencil"></i></a>';
						}
						$sectionsTable .= '</td>
									</tr>';
					}
					$sectionsTable .= '</tbody></table>';
					
				} else {
					$sectionsTable .= '<p>No sections found for this shelf.</p>';
				}
				
				echo '</div>';
				
				echo '<h2>Sections</h2>';
				echo $sectionsTable;
				?>
				<script>
				$('#link-addsection').colorbox({
					href: $('#link-addsection').attr('href')+'&smar_include=true',
					closeButton: false,
					width: '80%',
					maxWidth: '700px',
					onClosed: function() { $('#link-refresh').click(); }
				});
				$('.link-editsection').on('click', function(e) {
					
					e.preventDefault();
					$target = $(e.delegateTarget);
					$.colorbox({
						href: $target.attr('href')+'&smar_include=true',
						closeButton: false,
						width: '80%',
						maxWidth: '700px'
					});
				});
				setDesignerHandler('designer-canvas', '.canvas-section');
				setDesignerSaveHandler('#link-designer-save', '#designer-canvas', '.canvas-section');
				</script>
				<?php

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
		} else {
			$SMAR_MESSAGES['error'][] = 'Insufficient permissions for shelves management';
			smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES);
		}
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
							smar_update_shelf_svg($formID);
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
		if($_SESSION['loginRole'] >= 2) {
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
						// TODO: create shelf SVG here?
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
		} else {
			$SMAR_MESSAGES['error'][] = 'Insufficient permissions for shelves management';
			smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES);
		}
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
							<td>';
						if($_SESSION['loginRole'] >= 3) {
						echo '	<a href="'.$self.'?subpage=editshelf&id='.$row['shelf_id'].'" title="Edit" class="ajax"><i class="mdi mdi-pencil"></i></a>';
						}
						echo '	<a href="'.$self.'?subpage=designer&id='.$row['shelf_id'].'" title="Shelf Designer" class="ajax"><i class="mdi mdi-math-compass"></i></a>
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
}
?>
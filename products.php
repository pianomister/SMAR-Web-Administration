<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
*  products.php                     *
*                                   *
************************************/

require_once('_functions/_functions.php');

// set file name
$self = 'products.php';
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

#require_once('inc_session_check.php'); // TODO

// include subnav if requested
if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true') {
	
	?>
	<nav id="nav-page">
		<ul>
			<li><a href="<?php echo $self; ?>" <?php echo ($subpage == '') ? 'class="smar-active"' : ''; ?>>Products</a></li>
			<li><a href="<?php echo $self.'?subpage=addproduct'; ?>" <?php echo ($subpage == 'addproduct') ? 'class="smar-active"' : ''; ?>>Add product</a></li>
			<li><a href="<?php echo $self.'?subpage=units'; ?>" <?php echo ($subpage == 'units') ? 'class="smar-active"' : ''; ?>>Units</a></li>
			<li><a href="<?php echo $self.'?subpage=addunit'; ?>" <?php echo ($subpage == 'addunit') ? 'class="smar-active"' : ''; ?>>Add unit</a></li>
			<li><a href="<?php echo $self.'?subpage=mapping'; ?>" <?php echo ($subpage == 'mapping') ? 'class="smar-active"' : ''; ?>>Mappings</a></li>
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
		
		case 'mapping':
			?>
			<h1>Unit mappings</h1>
			<?php
			break;
		case 'units':
			?>
			<div class="flex">
				<h1>Units</h1>
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
						<input type="hidden" name="subpage" value="units">
						<input type="text" name="filter" placeholder="Filter by name" value="<?php if(isset($formFilter) && !empty($formFilter)) echo smar_form_input($formFilter); ?>" class="input-medium">
					</form>
					<?php

					// pagination
					$items_per_page = 2;	
					$current_page = 0;
					if(isset($_GET['limit']) && !empty($_GET['limit']))
						$current_page = intval($_GET['limit']);
					$limit = $items_per_page * $current_page;

					$result = $SMAR_DB->dbquery("SELECT count(*) as items FROM ".SMAR_MYSQL_PREFIX."_unit");
					$num_items = $result->fetch_array(MYSQLI_ASSOC)['items'];

					echo smar_pagination($self.'?page=products.php&subpage=units&filter='.$formFilter, $num_items, $items_per_page, $current_page);
					?>
				</div>
			</div>
			<table>
				<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Capacity</th>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody>
					<?php
					// get unit
					$result = $SMAR_DB->dbquery("SELECT p.unit_id, p.name, p.capacity
																				FROM ".SMAR_MYSQL_PREFIX."_unit p".$filter."
																				ORDER BY unit_id
																				LIMIT ".$SMAR_DB->real_escape_string($limit).",".$SMAR_DB->real_escape_string($items_per_page));
					if($result->num_rows > 0) {
						while($row = $result->fetch_array(MYSQLI_ASSOC)) {
							echo '<tr>
								<td>'.$row['unit_id'].'</td>
								<td>'.$row['name'].'</td>
								<td>'.$row['capacity'].'</td>
								<td>
									<a href="'.$self.'?subpage=editunit&id='.$row['unit_id'].'" title="Edit" class="ajax"><i class="mdi mdi-pencil"></i></a>
									<a href="'.$self.'?subpage=mapping&id='.$row['unit_id'].'" title="Show connected products" class="ajax"><i class="mdi mdi-cart"></i></a>
									<!--<a href="'.$self.'?subpage=deleteunit&id='.$row['unit_id'].'" title="Delete" class="ajax"><i class="mdi mdi-delete"></i></a>-->
								</td>
							</tr>';
						}
					} else {
						echo '<tr><td colspan="5">No units found</td></tr>';
					}
					?>
				</tbody>
			</table>
			<script>
			setFormHandler('#form-filter');
			</script>
			<?php
			break;
		case 'editunit':
		
			// set action type
			$page_action = 'edit';
		
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
				
				$formID = intval($_REQUEST['id']);
				
				// edit form was sent
				if(isset($_POST['send_editunit'])) {

					if(isset($_POST['form-unit-name']) &&
						 isset($_POST['form-unit-capacity'])
						) {

						$formName = strip_tags($_POST['form-unit-name']);
						$formCapacity = intval(strip_tags($_POST['form-unit-capacity']));
						
						if(!empty($_POST['form-unit-name']) &&
							 !empty($_POST['form-unit-capacity'])
							) {

							// init database
							if(!(isset($SMAR_DB))) {
								$SMAR_DB = new SMAR_MysqlConnect();
							}

							// get shelf data
							$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_unit SET
																						name = '".$SMAR_DB->real_escape_string($formName)."',
																						capacity = '".$SMAR_DB->real_escape_string($formCapacity)."'
																						WHERE unit_id = '".$SMAR_DB->real_escape_string($formID)."'");
							if($result === TRUE) {
								$SMAR_MESSAGES['success'][] = 'Changes for "'.$formName.'" were successfully saved.';
							} else {
								$SMAR_MESSAGES['error'][] = 'Inserting the changes for product "'.$formName.'" into database failed.';
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
					
					$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_unit WHERE unit_id = '".$SMAR_DB->real_escape_string($formID)."' LIMIT 1");
					if($result->num_rows == 1) {
						$row = $result->fetch_array(MYSQLI_ASSOC);
						
						$formName = $row['name'];
						$formCapacity = $row['capacity'];
						$formCreated = $row['created'];
						$formLastupdate = $row['lastupdate'];
						
					} else {
						$SMAR_MESSAGES['error'][] = 'No item was found for given ID '.smar_save_input($formID).'.';
					}
				}
				
			} else {
				$SMAR_MESSAGES['error'][] = 'No item ID was provided in URL parameters.';
			}
			
			?>
			<h1>Edit unit</h1>
			<?php
		case 'addunit':
		
			// set action type
			if(!isset($page_action))
				$page_action = 'add';
		
			// form was sent
			if($page_action == 'add' && isset($_POST['send_addunit'])) {

				if(isset($_POST['form-unit-name']) &&
					 isset($_POST['form-unit-capacity'])
					) {

						$formName = strip_tags($_POST['form-unit-name']);
						$formCapacity = intval(strip_tags($_POST['form-unit-capacity']));
						
						if(!empty($_POST['form-unit-name']) &&
							 !empty($_POST['form-unit-capacity'])
							) {

						// init database
						if(!(isset($SMAR_DB))) {
							$SMAR_DB = new SMAR_MysqlConnect();
						}

						// get shelf data
						$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_unit
																					(name, capacity, created) VALUES
																					('".$SMAR_DB->real_escape_string($formName)."', '".$SMAR_DB->real_escape_string($formCapacity)."', NOW())");
						if($result === TRUE) {
							$SMAR_MESSAGES['success'][] = 'Unit "'.$formName.'" was successfully created.';
						} else {
							$SMAR_MESSAGES['error'][] = 'Inserting the unit "'.$formName.'" into database failed.';
						}
					} else {
						$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
					}
				}
			}
		
			if($page_action == 'add')
				echo '<h1>Add unit</h1>';

			// print messages
			if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
			?>
			<form id="form-unit" method="post" action="index.php?page=<?php echo urlencode($self.'?subpage='.$page_action.'unit'); ?>">
				<div class="form-box swap-order">
					<input id="form-unit-name" type="text" name="form-unit-name" placeholder="Title or short description" value="<?php if(isset($formName)) echo smar_form_input($formName); ?>" />
					<label for="form-unit-name">Unit name</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-unit-capacity" type="text" name="form-unit-capacity" placeholder="0" value="<?php if(isset($formCapacity)) echo smar_form_input($formCapacity); ?>" />
					<label for="form-unit-capacity">Capacity</label>
				</div>
				<?php
				if($page_action == 'add') {
					echo '<input type="submit" value="Add unit" name="send_addunit" class="raised" />';
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
					<input type="submit" value="Save changes" name="send_editunit" class="raised" />
					<?php
				}
				?>
				<input type="reset" value="Reset form" name="reset" />
			</form>
			<!--AJAX Request-->
			<script>
			setFormHandler('#form-unit');
			</script>
			<?php
			break;

		case 'editproduct':
		
			// set action type
			$page_action = 'edit';
		
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
				
				$formID = intval($_REQUEST['id']);
				
				// edit form was sent
				if(isset($_POST['send_editproduct'])) {

					if(isset($_POST['form-product-name']) &&
						 isset($_POST['form-product-number']) &&
						 isset($_POST['form-product-price']) && 
						 isset($_POST['form-product-barcode']) &&
						 isset($_POST['form-product-image'])
						) {

						$formName = strip_tags($_POST['form-product-name']);
						$formNumber = strip_tags($_POST['form-product-number']);
						$formPrice = doubleval(strip_tags($_POST['form-product-price']));
						$formBarcode = intval(strip_tags($_POST['form-product-barcode']));
						$formImage = empty($_POST['form-product-image']) ? strip_tags($_POST['form-product-image']) : 'NULL';
						
						if(!empty($_POST['form-product-name']) &&
							 !empty($_POST['form-product-number']) &&
							 !empty($_POST['form-product-price']) &&
							 !empty($_POST['form-product-barcode'])
							) {

							// init database
							if(!(isset($SMAR_DB))) {
								$SMAR_DB = new SMAR_MysqlConnect();
							}

							// update
							$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_product SET
																						name = '".$SMAR_DB->real_escape_string($formName)."',
																						article_nr = '".$SMAR_DB->real_escape_string($formNumber)."',
																						price = '".$SMAR_DB->real_escape_string($formPrice)."',
																						barcode = '".$SMAR_DB->real_escape_string($formBarcode)."',
																						image = '".$SMAR_DB->real_escape_string($formImage)."'
																						WHERE product_id = '".$SMAR_DB->real_escape_string($formID)."'");
							if($result === TRUE) {
								$SMAR_MESSAGES['success'][] = 'Changes for "'.$formName.'" were successfully saved.';
							} else {
								$SMAR_MESSAGES['error'][] = 'Inserting the changes for product "'.$formName.'" into database failed.';
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
					
					$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_product WHERE product_id = '".$SMAR_DB->real_escape_string($formID)."' LIMIT 1");
					if($result->num_rows == 1) {
						$row = $result->fetch_array(MYSQLI_ASSOC);
						
						$formName = $row['name'];
						$formNumber = $row['article_nr'];
						$formPrice = $row['price'];
						$formBarcode = $row['barcode'];
						$formImage = $row['image'];
						$formCreated = $row['created'];
						$formLastupdate = $row['lastupdate'];
						
					} else {
						$SMAR_MESSAGES['error'][] = 'No item was found for given ID '.smar_save_input($formID).'.';
					}
				}
				
			} else {
				$SMAR_MESSAGES['error'][] = 'No item ID was provided in URL parameters.';
			}
			
			?>
			<h1>Edit product</h1>
			<?php
		case 'addproduct':
		
			// set action type
			if(!isset($page_action))
				$page_action = 'add';
		
			// form was sent
			if($page_action == 'add' && isset($_POST['send_addproduct'])) {

				if(isset($_POST['form-product-name']) &&
						 isset($_POST['form-product-number']) &&
						 isset($_POST['form-product-price']) && 
						 isset($_POST['form-product-barcode']) &&
						 isset($_POST['form-product-image'])
						) {

						$formName = strip_tags($_POST['form-product-name']);
						$formNumber = strip_tags($_POST['form-product-number']);
						$formPrice = doubleval(strip_tags($_POST['form-product-price']));
						$formBarcode = intval(strip_tags($_POST['form-product-barcode']));
						$formImage = empty($_POST['form-product-image']) ? strip_tags($_POST['form-product-image']) : 'NULL';
						
						if(!empty($_POST['form-product-name']) &&
							 !empty($_POST['form-product-number']) &&
							 !empty($_POST['form-product-price']) &&
							 !empty($_POST['form-product-barcode'])
							) {

						// init database
						if(!(isset($SMAR_DB))) {
							$SMAR_DB = new SMAR_MysqlConnect();
						}

						// insert
						$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_product
																					(name, article_nr, price, barcode, image, created) VALUES
																					('".$SMAR_DB->real_escape_string($formName)."', '".$SMAR_DB->real_escape_string($formNumber)."', '".$SMAR_DB->real_escape_string($formPrice)."', '".$SMAR_DB->real_escape_string($formBarcode)."', '".$SMAR_DB->real_escape_string($formImage)."', NOW())");
						if($result === TRUE) {
							$SMAR_MESSAGES['success'][] = 'Product "'.$formName.'" was successfully created.';
						} else {
							$SMAR_MESSAGES['error'][] = 'Inserting the product "'.$formName.'" into database failed.';
						}
					} else {
						$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
					}
				}
			}
		
			if($page_action == 'add')
				echo '<h1>Add product</h1>';

			// print messages
			if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
			?>
			<form id="form-product" method="post" action="index.php?page=<?php echo urlencode($self.'?subpage='.$page_action.'product'); ?>">
				<div class="form-box swap-order">
					<input id="form-product-name" type="text" name="form-product-name" placeholder="Title or short description" value="<?php if(isset($formName)) echo smar_form_input($formName); ?>" />
					<label for="form-product-name">Product name</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-product-number" type="text" name="form-product-number" placeholder="May contain non-numerical characters" value="<?php if(isset($formNumber)) echo smar_form_input($formNumber); ?>" />
					<label for="form-product-number">Article number</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-product-price" type="text" name="form-product-price" placeholder="0.00" value="<?php if(isset($formPrice)) echo smar_form_input($formPrice); ?>" />
					<label for="form-product-price">Price</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-product-barcode" type="text" name="form-product-barcode" placeholder="0123456789" value="<?php if(isset($formBarcode)) echo smar_form_input($formBarcode); ?>" />
					<label for="form-product-barcode">Barcode</label>
				</div>
				<div class="form-box swap-order">
					<input id="form-product-image" type="text" name="form-product-image" placeholder="http://" value="<?php if(isset($formImage)) echo smar_form_input($formImage); ?>" />
					<label for="form-product-image">Image URL (optional)</label>
				</div>
				<?php
				if($page_action == 'add') {
					echo '<input type="submit" value="Add product" name="send_addproduct" class="raised" />';
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
					<input type="submit" value="Save changes" name="send_editproduct" class="raised" />
					<?php
				}
				?>
				<input type="reset" value="Reset form" name="reset" />
			</form>
			<!--AJAX Request-->
			<script>
			setFormHandler('#form-product');
			</script>
			<?php
			break;
		default:
			?>
			<div class="flex">
				<h1>Products</h1>
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

					$result = $SMAR_DB->dbquery("SELECT count(*) as items FROM ".SMAR_MYSQL_PREFIX."_product");
					$num_items = $result->fetch_array(MYSQLI_ASSOC)['items'];

					echo smar_pagination($self.'?page=products.php&filter='.$formFilter, $num_items, $items_per_page, $current_page);
					?>
				</div>
			</div>
			<table>
				<thead>
				<tr>
					<th>ID</th>
					<th>Article No.</th>
					<th>Name</th>
					<th>Price</th>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody>
					<?php
					// get product
					$result = $SMAR_DB->dbquery("SELECT product_id, article_nr, name, price FROM ".SMAR_MYSQL_PREFIX."_product".$filter." ORDER BY product_id LIMIT ".$SMAR_DB->real_escape_string($limit).",".$SMAR_DB->real_escape_string($items_per_page));
					if($result->num_rows > 0) {
						while($row = $result->fetch_array(MYSQLI_ASSOC)) {
							echo '<tr>
								<td>'.$row['product_id'].'</td>
								<td>'.$row['article_nr'].'</td>
								<td>'.$row['name'].'</td>
								<td>'.$row['price'].'</td>
								<td>
									<a href="'.$self.'?subpage=editproduct&id='.$row['product_id'].'" title="Edit" class="ajax"><i class="mdi mdi-pencil"></i></a>
									<!--<a href="'.$self.'?subpage=deleteproduct&id='.$row['product_id'].'" title="Delete" class="ajax"><i class="mdi mdi-delete"></i></a>-->
								</td>
							</tr>';
						}
					} else {
						echo '<tr><td colspan="5">No products found</td></tr>';
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
	?>
</div>
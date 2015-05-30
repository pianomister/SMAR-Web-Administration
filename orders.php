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
$self = 'orders.php';
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

if($_SESSION['loginRole'] < 10) {
	$SMAR_MESSAGES['error'][] = 'Insufficient permissions for order management.';
	smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES);
} else {

// include subnav if requested
if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true') {
	
	?>
	<nav id="nav-page">
		<ul>
			<li><a href="<?php echo $self; ?>" <?php echo ($subpage == '') ? 'class="smar-active"' : ''; ?>>Orders</a></li>
			<li><a href="<?php echo $self.'?subpage=addorder'; ?>" <?php echo ($subpage == 'addorder') ? 'class="smar-active"' : ''; ?>>Add order</a></li>
			<li><a href="<?php echo $self.'?subpage=deliveries'; ?>" <?php echo ($subpage == 'deliveries') ? 'class="smar-active"' : ''; ?>>Deliveries</a></li>
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
	
		case 'deleteorder':
	
		if(isset($_GET['id']) && !empty($_GET['id'])) {

			$formID = intval($_GET['id']);
			
			// when confirmation was sent, delete
			if(isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
				
				// init database
				if(!(isset($SMAR_DB))) {
					$SMAR_DB = new SMAR_MysqlConnect();
				}
				
				// order cannot be deleted if it is part of orders
				$result = $SMAR_DB->dbquery("SELECT COUNT(*) as count FROM ".SMAR_MYSQL_PREFIX."_order_item WHERE order_id = '".$SMAR_DB->real_escape_string($formID)."'");
				$row = $result->fetch_array(MYSQLI_ASSOC);
				
				if($row['count'] > 0) {
					$SMAR_MESSAGES['error'][] = 'The order with ID "'.$formID.'" must not be deleted, because it already has order items. Deletion would cause inconsistencies in the orders system.';
				} else {
					// delete order
					$result = $SMAR_DB->dbquery("DELETE FROM ".SMAR_MYSQL_PREFIX."_order WHERE order_id = '".$SMAR_DB->real_escape_string($formID)."'");

					if($result === TRUE) {
						$SMAR_MESSAGES['success'][] = 'The order with ID "'.$formID.'" was successfully deleted.';
					} else {
						$SMAR_MESSAGES['error'][] = 'Deleting the order with ID "'.$formID.'" failed.';
					}
				}
				
			} else {
				$SMAR_MESSAGES['warning'][] = 'You are going to delete an order. When deleting an order, it must not have any related order items.<br> Do you really want to delete the order with ID "'.$formID.'"?';
			}
			
			?>
			<div id="orderDeleteContainer">
			<h1>Delete order (ID: <?php echo $formID; ?>)</h1>
			<?php
			/* print messages */ if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
			if(!isset($_GET['confirm'])) {
				?>
				<form id="form-order-delete" method="get" data-target="#orderDeleteContainer" action="index.php?page=<?php echo urlencode($self.'?subpage=deleteorder'); ?>">
					<input type="hidden" value="yes" name="confirm" />
					<input type="hidden" value="<?php echo $formID; ?>" name="id" />
					<input type="submit" value="Yes, delete order and related items" name="send_delete" class="raised" />
				</form>
				<!--AJAX Request-->
				<script>
				setFormHandler('#form-order-delete');
				</script>
				<?php
			}
			echo '</div>';
			
		} else {
			$SMAR_MESSAGES['error'][] = 'No item ID was provided in URL parameters.';
			/* print messages */ if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
		}
		break;
	case 'editorder':

		if($_SESSION['loginRole'] < 20) {
			$SMAR_MESSAGES['error'][] = 'Insufficient permissions for this action.';
			smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES);
			break;
		}
	
		// set action type
		$page_action = 'edit';

		if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {

			$formID = intval($_REQUEST['id']);

			// edit form was sent
			if(isset($_POST['send_editorder'])) {

				if(isset($_POST['form-order-name']) &&
					 isset($_POST['form-order-date']) &&
					 isset($_POST['form-order-barcode'])
					) {

					$formName = strip_tags($_POST['form-order-name']);
					$formDate = date("Y-m-d H:i:s", strtotime(strip_tags($_POST['form-order-date'])));
					$formBarcode = strip_tags($_POST['form-order-barcode']);

					if(!empty($_POST['form-order-name']) &&
						 !empty($_POST['form-order-date']) &&
						 !empty($_POST['form-order-barcode'])
						) {

						// init database
						if(!(isset($SMAR_DB))) {
							$SMAR_DB = new SMAR_MysqlConnect();
						}

						// update
						$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_order SET
																					name = '".$SMAR_DB->real_escape_string($formName)."',
																					date = '".$SMAR_DB->real_escape_string($formDate)."',
																					barcode = '".$SMAR_DB->real_escape_string($formBarcode)."'
																					WHERE order_id = '".$SMAR_DB->real_escape_string($formID)."'");
						if($result === TRUE) {
							$SMAR_MESSAGES['success'][] = 'Changes for "'.$formName.'" were successfully saved.';
						} else {
							$SMAR_MESSAGES['error'][] = 'Inserting the changes for order "'.$formName.'" into database failed.';
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

				$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_order
																		WHERE order_id = '".$SMAR_DB->real_escape_string($formID)."'
																					LIMIT 1");
				if($result->num_rows == 1) {
					$row = $result->fetch_array(MYSQLI_ASSOC);

					$formName = $row['name'];
					$formDate = $row['date'];
					$formBarcode = $row['barcode'];
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
		<h1>Edit order</h1>
		<?php
	case 'addorder':
		if($_SESSION['loginRole'] >= 30) {
			// set action type
			if(!isset($page_action))
				$page_action = 'add';

			// form was sent
			if($page_action == 'add' && isset($_POST['send_addorder'])) {

				if(isset($_POST['form-order-name']) &&
						 isset($_POST['form-order-date']) && 
						 isset($_POST['form-order-barcode'])
						) {

					$formName = strip_tags($_POST['form-order-name']);
					$formDate = date("Y-m-d H:i:s", strtotime(strip_tags($_POST['form-order-date'])));
					$formBarcode = strip_tags($_POST['form-order-barcode']);

					if(!empty($_POST['form-order-name']) &&
						 !empty($_POST['form-order-date']) &&
						 !empty($_POST['form-order-barcode'])
						) {

						// init database
						if(!(isset($SMAR_DB))) {
							$SMAR_DB = new SMAR_MysqlConnect();
						}

						// insert
						$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_order
																					(name, date, barcode, created) VALUES
																					('".$SMAR_DB->real_escape_string($formName)."', '".$SMAR_DB->real_escape_string($formDate)."', '".$SMAR_DB->real_escape_string($formBarcode)."', NOW())");

						if($result === TRUE) {
							$SMAR_MESSAGES['success'][] = 'Order "'.$formName.'" was successfully created.';
						} else {
							$SMAR_MESSAGES['error'][] = 'Inserting the order "'.$formName.'" into database failed.';
						}
					} else {
						$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
					}
				}
			}

			if($page_action == 'add')
				echo '<h1>Add order</h1>';

			// print messages
			if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }

			if($page_action == 'add' || $page_action == 'edit' && isset($formID)) {
				?>
				<form id="form-order" method="post" action="index.php?page=<?php echo urlencode($self.'?subpage='.$page_action.'order'); ?>">
					<div class="form-box swap-order">
						<input id="form-order-name" type="text" name="form-order-name" placeholder="Title or short description" value="<?php if(isset($formName)) echo smar_form_input($formName); ?>" />
						<label for="form-order-name">Order name</label>
					</div>
					<div class="form-box swap-order">
						<input id="form-order-date" type="text" name="form-order-date" placeholder="YYYY-MM-DD HH:MM" value="<?php if(isset($formDate)) echo smar_form_input($formDate); ?>" />
						<label for="form-order-date">Order date</label>
					</div>
					<div class="form-box swap-order">
						<input id="form-order-barcode" type="text" name="form-order-barcode" placeholder="1234567890" value="<?php if(isset($formBarcode)) echo smar_form_input($formBarcode); ?>" />
						<label for="form-order-barcode">Barcode</label>
					</div>
					
					<?php
					if($page_action == 'add') {
						echo '<input type="submit" value="Add order" name="send_addorder" class="raised" />';
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
						<input type="submit" value="Save changes" name="send_editorder" class="raised" />
						<?php
					}
					?>
					<input type="reset" value="Reset form" name="reset" />
				</form>
				<!--AJAX Request-->
				<script>
				setFormHandler('#form-order');
				</script>
				<?php
			}
		} else {
			$SMAR_MESSAGES['error'][] = 'Insufficient permissions for order management.';
			smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES);
		}
		break;
	default:
		?>
		<div class="flex">
			<h1>Orders</h1>
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
					$filter .= " WHERE name LIKE '%".$SMAR_DB->real_escape_string($formFilter)."%'";
				}
				?>
				<form id="form-filter" method="get" action="index.php?page=<?php echo urlencode($self); ?>">
					<input type="hidden" name="page" value="<?php echo urlencode($self); ?>">
					<input id="filter-orders" type="text" name="filter" placeholder="Filter by name" value="<?php if(isset($formFilter) && !empty($formFilter)) echo smar_form_input($formFilter); ?>" class="input-medium">
				</form>
				<?php

				// pagination
				$items_per_page = 20;	
				$current_page = 0;
				if(isset($_GET['limit']) && !empty($_GET['limit']))
					$current_page = intval($_GET['limit']);
				$limit = $items_per_page * $current_page;

				$result = $SMAR_DB->dbquery("SELECT count(*) as items FROM ".SMAR_MYSQL_PREFIX."_order");
				$num_items = $result->fetch_array(MYSQLI_ASSOC)['items'];

				echo smar_pagination($self.'?page=orders.php&filter='.$formFilter, $num_items, $items_per_page, $current_page);
				?>
			</div>
		</div>
		<table>
			<thead>
			<tr>
				<th>ID</th>
				<th>Date</th>
				<th>Name</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
				<?php
				// get order
				$result = $SMAR_DB->dbquery("SELECT order_id, date, name FROM ".SMAR_MYSQL_PREFIX."_order ".$filter." ORDER BY order_id LIMIT ".$SMAR_DB->real_escape_string($limit).",".$SMAR_DB->real_escape_string($items_per_page));
				if($result->num_rows > 0) {
					while($row = $result->fetch_array(MYSQLI_ASSOC)) {
						echo '<tr>
							<td>'.$row['order_id'].'</td>
							<td>'.$row['date'].'</td>
							<td>'.$row['name'].'</td>
							<td>';
							
							if($_SESSION['loginRole'] >= 30) {
								echo '<a href="'.$self.'?subpage=editorder&id='.$row['order_id'].'" title="Edit" class="ajax"><i class="mdi mdi-pencil"></i></a>
											<a href="'.$self.'?subpage=deleteorder&id='.$row['order_id'].'" title="Delete" class="link-deleteorder"><i class="mdi mdi-delete"></i></a>';
							}
						echo '</td></tr>';
					}
				} else {
					echo '<tr><td colspan="5">No orders found</td></tr>';
				}
				?>
			</tbody>
		</table>
		<!--AJAX Request-->
		<script>
		setFormHandler('#form-filter');
			
		$('.link-deleteorder').on('click', function(e) {

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
	}//end switch
	?>
</div>
<?php
}
?>
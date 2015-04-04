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
			<li><a href="<?php echo $self.'?subpage=newproduct'; ?>" <?php echo ($subpage == 'newproduct') ? 'class="smar-active"' : ''; ?>>Add product</a></li>
			<li><a href="<?php echo $self.'?subpage=units'; ?>" <?php echo ($subpage == 'units') ? 'class="smar-active"' : ''; ?>>Units</a></li>
			<li><a href="<?php echo $self.'?subpage=newunit'; ?>" <?php echo ($subpage == 'newunit') ? 'class="smar-active"' : ''; ?>>Add unit</a></li>
		</ul>
	</nav>
	<?php
}
?>
<div id="smar-content-inner">
	<?php

	// form was sent
	if(isset($_POST['send_newproduct'])) {

		if(isset($_POST['add-product-name']) && !empty($_POST['add-product-name']) &&
			 isset($_POST['add-product-number']) && !empty($_POST['add-product-number']) &&
			 isset($_POST['add-product-price']) && !empty($_POST['add-product-price']) &&
			 isset($_POST['add-product-barcode']) && !empty($_POST['add-product-barcode'])
			) {

			$addName = strip_tags($_POST['add-product-name']);
			$addNumber = strip_tags($_POST['add-product-number']);
			$addPrice = doubleval(strip_tags($_POST['add-product-price']));
			$addBarcode = intval(strip_tags($_POST['add-product-barcode']));
			$addImage = isset($_POST['add-product-image']) ? $_POST['add-product-image'] : NULL;

			// init database
			if(!(isset($SMAR_DB))) {
				$SMAR_DB = new SMAR_MysqlConnect();
			}

			// get shelf data
			$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_product
																		(name, article_nr, price, barcode, image, created) VALUES
																		('".$SMAR_DB->real_escape_string($addName)."', '".$SMAR_DB->real_escape_string($addNumber)."', '".$SMAR_DB->real_escape_string($addPrice)."', '".$SMAR_DB->real_escape_string($addBarcode)."', '".$SMAR_DB->real_escape_string($addImage)."', NOW())");
			if($result === TRUE) {
				$SMAR_MESSAGES['success'][] = 'Product "'.$addName.'" was successfully created.';
			} else {
				$SMAR_MESSAGES['error'][] = 'Inserting the product "'.$addName.'" into database failed.';
			}
		} else {
			$SMAR_MESSAGES['error'][] = 'Please fill in all required fields.';
		}
	}

	// print messages
	if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }

	// page content
	switch($subpage) {
		
		case 'units':
			?>
			<h1>Units</h1>
			<?php
			break;
		case 'newunit':
			?>
			<h1>Add unit</h1>
			<?php
			break;
		case 'editunit':
			?>
			<h1>Edit unit</h1>
			<?php
			break;
		case 'newproduct':
			?>
			<h1>Add product</h1>
			<form id="form-add-product" method="post" action="index.php?page=<?php echo urlencode($self.'?subpage=newproduct'); ?>">
				<div class="form-box swap-order">
					<input id="add-product-name" type="text" name="add-product-name" placeholder="Title or short description" value="<?php if(isset($_POST['add-product-name'])) echo smar_form_input($_POST['add-product-name']); ?>" />
					<label for="add-product-name">Product name</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-product-number" type="text" name="add-product-number" placeholder="May contain non-numerical characters" value="<?php if(isset($_POST['add-product-number'])) echo smar_form_input($_POST['add-product-number']); ?>" />
					<label for="add-product-number">Article number</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-product-price" type="text" name="add-product-price" placeholder="0.00" value="<?php if(isset($_POST['add-product-price'])) echo smar_form_input($_POST['add-product-price']); ?>" />
					<label for="add-product-price">Price</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-product-barcode" type="text" name="add-product-barcode" placeholder="0123456789" value="<?php if(isset($_POST['add-product-barcode'])) echo smar_form_input($_POST['add-product-barcode']); ?>" />
					<label for="add-product-barcode">Barcode</label>
				</div>
				<div class="form-box swap-order">
					<input id="add-product-image" type="text" name="add-product-image" placeholder="http://" value="<?php if(isset($_POST['add-product-image'])) echo smar_form_input($_POST['add-product-image']); ?>" />
					<label for="add-product-image">Image URL (optional)</label>
				</div>
				<input type="submit" value="Add product" name="send_newproduct" class="raised" />
			</form>
			<!--AJAX Request-->
			<script>
			setFormHandler('#form-add-product');
			</script>
			<?php
			break;
		case 'editproduct':
			?>
			<h1>Edit product</h1>
			<?php
			break;
		default:
			?>
			<h1>Products</h1>
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
					// init database
					if(!(isset($SMAR_DB))) {
						$SMAR_DB = new SMAR_MysqlConnect();
					}

					// get products
					$result = $SMAR_DB->dbquery("SELECT product_id, article_nr, name, price FROM ".SMAR_MYSQL_PREFIX."_product LIMIT 100");
					if($result->num_rows > 0) {
						while($row = $result->fetch_array(MYSQLI_ASSOC)) {
							echo '<tr>
								<td>'.$row['product_id'].'</td>
								<td>'.$row['article_nr'].'</td>
								<td>'.$row['name'].'</td>
								<td>'.$row['price'].'</td>
								<td>
									<a href="'.$self.'?subpage=editproduct&id='.$row['product_id'].'" title="Edit"><i class="mdi mdi-pencil"></i></a>
									<a href="'.$self.'?subpage=deleteproduct&id='.$row['product_id'].'" title="Delete"><i class="mdi mdi-delete"></i></a>
								</td>
							</tr>';
						}
					} else {
						echo '<tr><td colspan="5">No products found</td></tr>';
					}
					?>
				</tbody>
			</table>
			<?php
	}
	?>
</div>
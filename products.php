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
	// TODO: print messages

	// page content
	switch($subpage) {
		
		case 'unit':
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
					<tr>
						<td>42</td>
						<td>3423564</td>
						<td>Duschgel ACME Men Sports</td>
						<td>1.69</td>
						<td>
						<svg style="width:24px;height:24px" viewBox="0 0 24 24">
							<path fill="#000000" d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" />
						</svg>
						<svg style="width:24px;height:24px" viewBox="0 0 24 24">
							<path fill="#000000" d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
						</svg>
						</td>
					</tr>
					<tr>
						<td>481</td>
						<td>2497564</td>
						<td>LekkaLekka Crunchy Bio-Chips</td>
						<td>2.99</td>
						<td>
						<svg style="width:24px;height:24px" viewBox="0 0 24 24">
							<path fill="#000000" d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" />
						</svg>
						<svg style="width:24px;height:24px" viewBox="0 0 24 24">
							<path fill="#000000" d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
						</svg>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
	}
	?>
</div>
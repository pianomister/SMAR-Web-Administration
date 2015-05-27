<?php
require '../_functions/Slim/Slim.php';
require_once '../_functions/_functions.php';
require_once '../_functions/_jwt/JWT.php';

if(isset($_REQUEST['jwt']))
	$jwt = $_REQUEST['jwt'];
else
	$jwt = "";

function checkLogin($jwtToken) {
	$decoded = JWT::decode($jwtToken, SMAR_JWT_SSK, array('HS256'));
	if($decoded['device'] == true) {
		$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_device WHERE hwaddress = '".$SMAR_DB->real_escape_string($decoded['hwaddress'])."' AND activated = 1");
		if($result->num_rows != 0) {
			$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_user WHERE username = '".$SMAR_DB->real_escape_string($decoded['user'])."'");
			if($result->num_rows != 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_user WHERE user_id = '".$SMAR_DB->real_escape_string($decoded['user_id'])."' AND username = '".$SMAR_DB->real_escape_string($decoded['username'])."' AND role_web = '".$SMAR_DB->real_escape_string($decoded['user_role'])."'");
		if($result->num_rows != 0) {
			return true;
		} else {
			return false;
		}
	}
}

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->contentType('application/json;charset=utf-8');

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 *
 * DOCS: http://docs.slimframework.com/
 */



 /**
 * only a short check
 */
 $app->get('/connection/check', function() use($app) {
	$resultArray['ready'] = SMAR_REST_API_READY;
	$res = $app->response();
	$res->setBody(json_encode($resultArray));
})->name('check_connection');



/**
 * returns a list of users that have authorization to use devices
 */
$app->get('/users/device', function() use($app) {
	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}

	$result = $SMAR_DB->dbquery("SELECT username FROM ".SMAR_MYSQL_PREFIX."_user WHERE role_device = '1'");
	if($result->num_rows != 0) {
		
			$resultArray = array();
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$resultArray[] = $row;
			}
		
			$response = json_encode($resultArray);
			$res = $app->response();
			$res->setBody($response);
	} else {
		$res = $app->response();
		$res->setBody('[]');
	}
})->name('list_device_users');



/**
 * get product with given ID
 */
$app->get('/getProduct/:product_code', function($product_code) use($app) {
	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
	
	$result = $SMAR_DB->dbquery("SELECT * 
			FROM ".SMAR_MYSQL_PREFIX."_product p, ".SMAR_MYSQL_PREFIX."_stock s, ".SMAR_MYSQL_PREFIX."_product_unit pu 
			WHERE p.barcode= '".$SMAR_DB->real_escape_string($product_code)."' 
				  AND s.product_id = p.product_id 
				  AND pu.product_id = p.product_id");
	if($result->num_rows != 0) {
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$resultArray[] = $row;
		}
	
		$response = json_encode($resultArray);
		$res = $app->response();
		$res->setBody($response); 
	} else {
		$res = $app->response();
		$res->setBody('[{}]');
	}
})->name('get_productInformation');

/** 
 * get all units
*/
$app->get('/getUnits', function() use($app) {
	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
	
	$result = $SMAR_DB->dbquery("SELECT name, capacity FROM ".SMAR_MYSQL_PREFIX."_unit");
	if($result->num_rows > 0 ) 
	{
		$resultArray = array();
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$resultArray[] = $row;
		}
		$response = json_encode($resultArray);
		$res = $app->response();
		$res->setBody($response);
	}
	else {
		$res = $app->response();
		$res->setBody('[{}]');
	}
})->name('allUnits');

/* updateStock of Product 
 *
 */
 $app->post('/updateProductStock', function() use($app) {
	if(isset($_POST['product_id']) && isset($_POST['amount'])) {
	$product_id = $_POST['product_id'];
	$amount = $_POST['amount'];

	$return = array();
		// init database
		if(!(isset($SMAR_DB))) {
			$SMAR_DB = new SMAR_MysqlConnect();
		}
		
		$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_stock 
					SET amount_warehouse = amount_warehouse - ".$SMAR_DB->real_escape_string($amount).", 
					 amount_shop = amount_shop + ".$SMAR_DB->real_escape_string($amount)." 
					WHERE product_id = ".$SMAR_DB->real_escape_string($product_id)."");
		
		
		
		if(count($return) > 0) {
				$response = json_encode($return);
				$res = $app->response();
				$res->setStatus(200);//TODO reset on 500
				$res->setBody($response);
			} else {
				$return['success'] = 'success';
				$response = json_encode($return);
				$res = $app->response();
				$res->setStatus(200);
				$res->setBody($response);
		}
	}
	else {
		$return['reason'] = 'missing parameters';
		$response = json_encode($return);
		$res = $app->response();
		$res->setStatus(500);
		$res->setBody($response);
	}
 })->name('updateStock');

/**
 * authenticate with JWT
 */
$app->post('/authentication', function () use ($app) {
	
	$hwaddress = $_POST['hwaddress'];
	$user = $_POST['user'];
	$password = $_POST['password'];
	$return = array();
	
	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
	
	$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_device WHERE hwaddress = '".$SMAR_DB->real_escape_string($hwaddress)."' AND activated = 1");
	if($result->num_rows != 0) {
		$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_user WHERE username = '".$SMAR_DB->real_escape_string($user)."' AND password_device = '".$SMAR_DB->real_escape_string($password)."'");
		if($result->num_rows != 0) {
			$row = $result->fetch_array();
			if($row['role_device'] == 1) {
				$token = array(
					"hwaddress" => $hwaddress,
					"user" => $user,
					"device" => "true"
				);
				$return['jwt'] = JWT::encode($token, SMAR_JWT_SSK);
				$response = json_encode($return);
				$res = $app->response();
				$res->setStatus(200);
				$res->setBody($response);
			} else {
				$return['reason'] = "permission";
				$response = json_encode($return);
				$res = $app->response();
				$res->setStatus(401);
				$res->setBody($response);
			}
		} else {
			$return['reason'] = "password";
			$response = json_encode($return);
			$res = $app->response();
			$res->setStatus(403);
			$res->setBody($response);
		}
	} else {
		$return['reason'] = "device";
		$response = json_encode($return);
		$res = $app->response();
		$res->setStatus(403);
		$res->setBody($response);
	}
})->name('authentication');

/**
 * get svg graphics for a file (newer than timestamp)
 */
$app->get('/svg/(:timestamp)', function ($timestamp = 0) use($app) {

	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
	
	$timestamp = date("Y-m-d H:i:s", $timestamp);
	
	$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_shelf_graphic WHERE lastupdate >= '".$SMAR_DB->real_escape_string($timestamp)."'");
	if($result->num_rows != 0) {
		
			$resultArray = array();
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$resultArray[] = $row;
			}
		
			$response = json_encode($resultArray);
			$res = $app->response();
			$res->setBody($response);
	} else {
		$res = $app->response();
		$res->setBody('[]');
	}
})->name('shelf_grpahics_newer_than_timestamp');


/**
 * get sections for a shelf (found by id)
 */
$app->get('/sections/:shelfid', function ($shelfid) use($app) {

	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
	
	$shelfid = intval($shelfid);

	$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_section WHERE shelf_id = '".$SMAR_DB->real_escape_string($shelfid)."'");
	if($result->num_rows != 0) {
		
			$resultArray = array();
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$resultArray[] = $row;
			}
		
			$response = json_encode($resultArray);
			$res = $app->response();
			$res->setBody($response);
	} else {
		$res = $app->response();
		$res->setBody('[{}]');
	}
})->name('sections_by_shelf_id');



/**
 * get database entry for a barcode
 * returns first result for a barcode
 */
$app->get('/barcode/:barcode', function ($barcode) use($app) {

	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
	
	$barcode = intval($barcode);

	$tables = array('product','product_unit','shelf');
	for($i = 0; $i < count($tables);$i++) {
		$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_".$tables[$i]."
																	WHERE barcode = '".$SMAR_DB->real_escape_string($barcode)."'
																	LIMIT 1");
		if($result->num_rows != 0)
			break;
	}
	
	if($result->num_rows != 0) {
		
			$resultArray = array();
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$resultArray[] = $row;
			}
		
			$response = json_encode($resultArray);
			$res = $app->response();
			$res->setBody($response);
	} else {
		$res = $app->response();
		$res->setBody('[{}]');
	}
})->name('object_by_barcode');



/**
 * autocomplete services
 * returns list of results matching the search term
 */
$app->get('/search/:table/:search(/:limit)', function ($table, $search, $limit = 5) use ($app) {

	$table_whitelist = array('product', 'unit', 'shelf', 'section');
	$table = strtolower($table);
	$limit = intval($limit);
	
	if(in_array($table, $table_whitelist)) {

		// init database
		if(!(isset($SMAR_DB))) {
			$SMAR_DB = new SMAR_MysqlConnect();
		}
		
		$constraints = " OR ".$table."_id LIKE '%".$SMAR_DB->real_escape_string($search)."%'";
		if($table == 'product')
			$constraints .= " OR article_nr LIKE '%".$SMAR_DB->real_escape_string($search)."%'";

		$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_".$table." WHERE name LIKE '%".$SMAR_DB->real_escape_string($search)."%' ".$constraints." LIMIT ".$SMAR_DB->real_escape_string($limit));
		if($result->num_rows != 0) {

				$resultArray = array();
				while($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$resultArray[] = $row;
				}
			
				$response = json_encode($resultArray);
			
				$res = $app->response();
				$res->setBody($response);

		} else {
			$res = $app->response();
			$res->setBody('[{}]');
		}
	
	} else {
		$res = $app->response();
		$res->setStatus(404);
		$res->setBody('[{}]');
	}
})->name('search_in_names');



/**
 * update shelf designer service
 * saves positions for shelves from designer canvas
 */
$app->post('/mappings/update/', function () use ($app) {

	if(isset($_POST['data']) &&
		 isset($_POST['type']) &&
		 isset($_POST['id'])) {

		// get parameters from POST request
		$mappings = json_decode($_POST['data']);
		$itemid = intval($_POST['id']);
		$return = array();

		$types = array('unit', 'product');
		$type1 = array_search($_POST['type'], $types);
		$type2 = $types[($type1+1)%2];
		$type1 = $types[$type1];

		// init database
		if(!(isset($SMAR_DB))) {
			$SMAR_DB = new SMAR_MysqlConnect();
		}

		foreach($mappings as $mapping) {

			$mapping->id = intval($mapping->id);
			$mapping->barcode = strip_tags($mapping->barcode);
			if(isset($mapping->product_unit_id))
				$mapping->product_unit_id = intval($mapping->product_unit_id);

			$result = NULL;
			
			switch($mapping->action) {
				case 'add':

					$result = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_product_unit
													(".$SMAR_DB->real_escape_string($type1)."_id, ".$SMAR_DB->real_escape_string($type2)."_id, barcode, created) VALUES
													('".$SMAR_DB->real_escape_string($itemid)."', '".$SMAR_DB->real_escape_string($mapping->id)."',
														'".$SMAR_DB->real_escape_string($mapping->barcode)."', NOW())");

					if(!$result)
						$return[] = $mapping;

					break;
				case 'change':

					if(isset($mapping->product_unit_id)) {
						
						$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_product_unit SET
													barcode = '".$SMAR_DB->real_escape_string($mapping->barcode)."'									
													WHERE product_unit_id = '".$SMAR_DB->real_escape_string($mapping->product_unit_id)."'");
						
						$return[] = "UPDATE ".SMAR_MYSQL_PREFIX."_product_unit SET
													barcode = '".$SMAR_DB->real_escape_string($mapping->barcode)."'									
													WHERE product_unit_id = '".$SMAR_DB->real_escape_string($mapping->product_unit_id)."'";
						
						if(!$result)
							$return[] = $mapping;
					} else {
						$return[] = $mapping;
					}
				
					break;
				case 'delete':
				
					if(isset($mapping->product_unit_id)) {
						
						$result = $SMAR_DB->dbquery("DELETE FROM ".SMAR_MYSQL_PREFIX."_product_unit						
													WHERE product_unit_id = '".$SMAR_DB->real_escape_string($mapping->product_unit_id)."'");
						
						if(!$result)
							$return[] = $mapping;
					} else {
						$return[] = $mapping;
					}
					
					break;
				case 'none':
					break;
				default:
					$return['reason'] = 'mapping definition without valid action type';
					$return[] = $mapping;
					break;
			}
		}

		if(count($return) > 0) {
			$response = json_encode($return);
			$res = $app->response();
			$res->setStatus(200);//TODO reset on 500
			$res->setBody($response);
		} else {
			$response = json_encode($return);
			$res = $app->response();
			$res->setStatus(200);
			$res->setBody($response);
		}
	} else {
		$return['reason'] = 'missing parameters';
		$response = json_encode($return);
		$res = $app->response();
		$res->setStatus(500);
		$res->setBody($response);
	}
	
})->name('update_mappings');



/**
 * update shelf designer service
 * saves positions for shelves from designer canvas
 */
$app->post('/designer/update/:shelfid', function ($shelfid) use ($app) {
	
	// get parameters from PUT request
	//parse_str(file_get_contents("php://input"), $_MY_PUT);//TODO
	//$sections = json_decode($_MY_PUT['data']);
	$sections = json_decode($_POST['data']);
	$shelfid = intval($shelfid);
	$return = array();
	
	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
	
	foreach($sections as $section) {

		$section->section_id = intval($section->section_id);
		$section->size_x = intval($section->size_x);
		$section->size_y = intval($section->size_y);
		$section->position_x = intval($section->position_x);
		$section->position_y = intval($section->position_y);
		
		$result = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_section SET
								size_x = '".$SMAR_DB->real_escape_string($section->size_x)."',
								size_y = '".$SMAR_DB->real_escape_string($section->size_y)."',
								position_x = '".$SMAR_DB->real_escape_string($section->position_x)."',
								position_y = '".$SMAR_DB->real_escape_string($section->position_y)."'
							WHERE section_id = '".$SMAR_DB->real_escape_string($section->section_id)."'
							AND shelf_id = '".$SMAR_DB->real_escape_string($shelfid)."'");
		
		if(!$result) {
			$return[] = $section;
		}
	}
	
	if(count($return) > 0) {
		$response = json_encode($return);
		$res = $app->response();
		$res->setStatus(500);
		$res->setBody($response);
	} else {
		// update shelf SVG in database
		$update = smar_update_shelf_svg($shelfid);
		
		$return['updateSVG'] = $update;
		
		$response = json_encode($return);
		$res = $app->response();
		$res->setStatus(200);
		$res->setBody($response);
	}
	
})->name('update_shelf_designer');

	

/**
 * root folder, prints all APIs
 */
$app->get('/', function () use($app) {
/*        $template = <<<EOT
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>Slim Framework for PHP 5</title>
            <style>
                html,body,div,span,object,iframe,
                h1,h2,h3,h4,h5,h6,p,blockquote,pre,
                abbr,address,cite,code,
                del,dfn,em,img,ins,kbd,q,samp,
                small,strong,sub,sup,var,
                b,i,
                dl,dt,dd,ol,ul,li,
                fieldset,form,label,legend,
                table,caption,tbody,tfoot,thead,tr,th,td,
                article,aside,canvas,details,figcaption,figure,
                footer,header,hgroup,menu,nav,section,summary,
                time,mark,audio,video{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent;}
                body{line-height:1;}
                article,aside,details,figcaption,figure,
                footer,header,hgroup,menu,nav,section{display:block;}
                nav ul{list-style:none;}
                blockquote,q{quotes:none;}
                blockquote:before,blockquote:after,
                q:before,q:after{content:'';content:none;}
                a{margin:0;padding:0;font-size:100%;vertical-align:baseline;background:transparent;}
                ins{background-color:#ff9;color:#000;text-decoration:none;}
                mark{background-color:#ff9;color:#000;font-style:italic;font-weight:bold;}
                del{text-decoration:line-through;}
                abbr[title],dfn[title]{border-bottom:1px dotted;cursor:help;}
                table{border-collapse:collapse;border-spacing:0;}
                hr{display:block;height:1px;border:0;border-top:1px solid #cccccc;margin:1em 0;padding:0;}
                input,select{vertical-align:middle;}
                html{ background: #EDEDED; height: 100%; }
                body{background:#FFF;margin:0 auto;min-height:100%;padding:0 30px;width:440px;color:#666;font:14px/23px Arial,Verdana,sans-serif;}
                h1,h2,h3,p,ul,ol,form,section{margin:0 0 20px 0;}
                h1{color:#333;font-size:20px;}
                h2,h3{color:#333;font-size:14px;}
                h3{margin:0;font-size:12px;font-weight:bold;}
                ul,ol{list-style-position:inside;color:#999;}
                ul{list-style-type:square;}
                code,kbd{background:#EEE;border:1px solid #DDD;border:1px solid #DDD;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;padding:0 4px;color:#666;font-size:12px;}
                pre{background:#EEE;border:1px solid #DDD;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;padding:5px 10px;color:#666;font-size:12px;}
                pre code{background:transparent;border:none;padding:0;}
                a{color:#70a23e;}
                header{padding: 30px 0;text-align:center;}
            </style>
        </head>
        <body>
            <header>
                <a href="http://www.slimframework.com"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHIAAAA6CAYAAABs1g18AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABRhJREFUeNrsXY+VsjAMR98twAo6Ao4gI+gIOIKOgCPICDoCjCAjXFdgha+5C3dcv/QfFB5i8h5PD21Bfk3yS9L2VpGnlGW5kS9wJMTHNRxpmjYRy6SycgRvL18OeMQOTYQ8HvIoJKiiz43hgHkq1zvK/h6e/TyJQXeV/VyWBOSHA4C5RvtMAiCc4ZB9FPjgRI8+YuKcrySO515a1hoAY3nc4G2AH52BZsn+MjaAEwIJICKAIR889HljMCcyrR0QE4v/q/BVBQva7Q1tAczG18+x+PvIswHEAslLbfGrMZKiXEOMAMy6LwlisQCJLPFMfKdBtli5dIihRyH7A627Iaiq5sJ1ThP9xoIgSdWSNVIHYmrTQgOgRyRNqm/M5PnrFFopr3F6B41cd8whRUSufUBU5EL4U93AYRnIWimCIiSI1wAaAZpJ9bPnxx8eyI3Gt4QybwWa6T/BvbQECUMQFkhd3jSkPFgrxwcynuBaNT/u6eJIlbGOBWSNIUDFEIwPZFAtBfYrfeIOSRSXuUYCsprCXwUIZWYnmEhJFMIocMDWjn206c2EsGLCJd42aWSyBNMnHxLEq7niMrY2qyDbQUbqrrTbwUPtxN1ZZCitQV4ZSd6DyoxhmRD6OFjuRUS/KdLGRHYowJZaqYgjt9Lchmi3QYA/cXBsHK6VfWNR5jgA1DLhwfFe4HqfODBpINEECCLO47LT/+HSvSd/OCOgQ8qE0DbHQUBqpC4BkKMPYPkFY4iAJXhGAYr1qmaqQDbECCg5A2NMchzR567aA4xcRKclI405Bmt46vYD7/Gcjqfk6GP/kh1wovIDSHDfiAs/8bOCQ4cf4qMt7eH5Cucr3S0aWGFfjdLHD8EhCFvXQlSqRrY5UV2O9cfZtk77jUFMXeqzCEZqSK4ICkSin2tE12/3rbVcE41OBjBjBPSdJ1N5lfYQpIuhr8axnyIy5KvXmkYnw8VbcwtTNj7fDNCmT2kPQXA+bxpEXkB21HlnSQq0gD67jnfh5KavVJa/XQYEFSaagWwbgjNA+ywstLpEWTKgc5gwVpsyO1bTII+tA6B7BPS+0PiznuM9gPKsPVXbFdADMtwbJxSmkXWfRh6AZhyyzBjIHoDmnCGaMZAKjd5hyNJYCBGDOVcg28AXQ5atAVDO3c4dSALQnYblfa3M4kc/cyA7gMIUBQCTyl4kugIpy8yA7ACqK8Uwk30lIFGOEV3rPDAELwQkr/9YjkaCPDQhCcsrAYlF1v8W8jAEYeQDY7qn6tNGWudfq+YUEr6uq6FZzBpJMUfWFDatLHMCciw2mRC+k81qCCA1DzK4aUVfrJpxnloZWCPVnOgYy8L3GvKjE96HpweQoy7iwVQclVutLOEKJxA8gaRCjSzgNI2zhh3bQhzBCQQPIHGaHaUd96GJbZz3Smmjy16u6j3FuKyNxcBarxqWWfYFE0tVVO1Rl3t1Mb05V00MQCJ71YHpNaMcsjWAfkQvPPkaNC7LqTG7JAhGXTKYf+VDeXAX9IvURoAwtTFHvyYIxtnd5tPkywrPafcwbeSuGVwFau3b76NO7SHQrvqhfFE8kM0Wvpv8gVYiYBlxL+fW/34bgP6bIC7JR7YPDubcHCPzIp4+cum7U6NlhZgK7lua3KGLeFwE2m+HblDYWSHG2SAfINuwBBfxbJEIuWZbBH4fAExD7cvaGVyXyH0dhiAYc92z3ZDfUVv+jgb8HrHy7WVO/8BFcy9vuTz+nwADAGnOR39Yg/QkAAAAAElFTkSuQmCC" alt="Slim"/></a>
            </header>
            <h1>Welcome to Slim!</h1>
            <p>
                Congratulations! Your Slim application is running. If this is
                your first time using Slim, start with this <a href="http://docs.slimframework.com/#Hello-World" target="_blank">"Hello World" Tutorial</a>.
            </p>
            <section>
                <h2>Get Started</h2>
                <ol>
                    <li>The application code is in <code>index.php</code></li>
                    <li>Read the <a href="http://docs.slimframework.com/" target="_blank">online documentation</a></li>
                    <li>Follow <a href="http://www.twitter.com/slimphp" target="_blank">@slimphp</a> on Twitter</li>
                </ol>
            </section>
            <section>
                <h2>Slim Framework Community</h2>

                <h3>Support Forum and Knowledge Base</h3>
                <p>
                    Visit the <a href="http://help.slimframework.com" target="_blank">Slim support forum and knowledge base</a>
                    to read announcements, chat with fellow Slim users, ask questions, help others, or show off your cool
                    Slim Framework apps.
                </p>

                <h3>Twitter</h3>
                <p>
                    Follow <a href="http://www.twitter.com/slimphp" target="_blank">@slimphp</a> on Twitter to receive the very latest news
                    and updates about the framework.
                </p>
            </section>
            <section style="padding-bottom: 20px">
                <h2>Slim Framework Extras</h2>
                <p>
                    Custom View classes for Smarty, Twig, Mustache, and other template
                    frameworks are available online in a separate repository.
                </p>
                <p><a href="https://github.com/codeguy/Slim-Extras" target="_blank">Browse the Extras Repository</a></p>
            </section>
        </body>
    </html>
EOT;
	echo $template;*/

	// API index
	// Display all routes and their URL
	$routes = $app->router()->getNamedRoutes();
	$result = array();
	foreach($routes as $route) {
		$result[$route->getName()] = $route->getPattern();
	}
	// Format JSON output
	$result = json_encode($result, JSON_PRETTY_PRINT);
	$result = stripslashes($result);

	$res = $app->response();
  $res->setBody($result);

  $array = $res->finalize();
});




/*
// POST route
$app->post(
    '/post',
    function () {
        echo 'This is a POST route';
    }
);

// PUT route
$app->put(
    '/put',
    function () {
        echo 'This is a PUT route';
    }
);

// PATCH route
$app->patch('/patch', function () {
    echo 'This is a PATCH route';
});

// DELETE route
$app->delete(
    '/delete',
    function () {
        echo 'This is a DELETE route';
    }
);*/

$app->run();

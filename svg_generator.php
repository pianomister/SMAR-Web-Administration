<?php
require_once('_functions/_functions.php');
header('Content-Type: image/svg+xml');

$tplSVG = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">
<svg width="{{shelfX}}" height="{{shelfY}}" viewBox="0 0 {{shelfX}} {{shelfY}}" style="width:100%;height: auto;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
	<title>Shelf \'{{shelfName}}\' (ID: {{shelfID}}, last updated: \'{{time}}\')</title>
	
	<defs>
		<style type="text/css">
		<![CDATA[
		text {fill: #333;font-family:Roboto;font-size: 8px;}
		rect {fill:#ccc; stroke:#777; stroke-width: 2px;}
		.section {fill:#ddd; stroke:#555; stroke-width: 1px; opacity:.8;}
		.selected {fill:#16a082;}
		]]>
		</style>
	</defs>
	
	<rect id="shelf{{shelfID}}" x="0" y="0" width="{{shelfX}}" height="{{shelfY}}" />
	{{sections}}
</svg>
';
$tplSection = '<rect id="section{{sectionID}}" x="{{sectionPosX}}" y="{{sectionPosY}}" width="{{sectionX}}" height="{{sectionY}}" class="section" />
							 <text x="{{sectionTextX}}" y="{{sectionTextY}}">{{sectionID}}: {{sectionName}}</text>';

// check if id for shelf is given
if(isset($_GET['id'])) {
	$id = intval($_GET['id']);
	
	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}

	// get shelf data
	$result = $SMAR_DB->dbquery("SELECT shelf_id, name, size_x, size_y, lastupdate FROM ".SMAR_MYSQL_PREFIX."_shelf WHERE shelf_id = '".$SMAR_DB->real_escape_string($id)."' LIMIT 1");

	if($result->num_rows == 1) {
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$result->free();

		// insert shelf information into template
		$tpl_search = array('{{shelfID}}','{{shelfName}}','{{time}}','{{shelfX}}','{{shelfY}}');
		$tpl_replace = array($row['shelf_id'], $row['name'], date('d.m.Y H:i:s', strtotime($row['lastupdate'])), $row['size_x'], $row['size_y']);
		$tplSVG = str_replace($tpl_search, $tpl_replace, $tplSVG);

		// get sections
		$sections = '';
		$result_sections = $SMAR_DB->dbquery("SELECT section_id, name, size_x, size_y, position_x, position_y FROM ".SMAR_MYSQL_PREFIX."_section WHERE shelf_id = '".$row['shelf_id']."'");
		
		if($result_sections->num_rows > 0) {	
			
			$tmp_search = array('{{sectionID}}',
													'{{sectionPosX}}',
													'{{sectionPosY}}',
													'{{sectionX}}',
													'{{sectionY}}',
													'{{sectionName}}',
													'{{sectionTextX}}',
													'{{sectionTextY}}');
			
			while($section = $result_sections->fetch_array(MYSQLI_ASSOC)) {
				$tmp = $tplSection;
				
				$tmp_replace = array($section['section_id'],
														 $section['position_x'],
														 $section['position_y'],
														 $section['size_x'],
														 $section['size_y'],
														 $section['name'],
														 $section['position_x']+5,
														 $section['position_y']+15);
				
				$tmp = str_replace($tmp_search, $tmp_replace, $tmp);
				$sections .= $tmp;
			}
			
			$result_sections->free();
		}
		
		$tplSVG = str_replace('{{sections}}', $sections, $tplSVG);
		
		echo $tplSVG;
	}
}

?>
<?php

/* Our Google Base API developer key. */
$developerKey = "ABQIAAAA6ggeUfjN1SpHwYsrpccTGhRuQWnos65R7rFyIjvnCKH4e1YArxSdx2HKFtraZCwQgrQplEXLG99isg";

/* The items feed URL, used for queries, insertions and batch commands. */
$itemsFeedURL = "http://www.google.com/base/feeds/items";

/* Parsed recipe entries from a query. */
$parsedEntries = array();

/* Are we currently parsing an XML ENTRY tag? */
$foundEntry = false;

/* Current XML element being processed. */
$curElement = "";

/* Types of cuisine the user may select when inserting a recipe. */
$cuisines = array('African', 'American', 'Asian', 'Caribbean', 'Chinese', 'French', 'Greek', 'Indian', 'Italian', 'Japanese', 'Jewish', 'Mediterranean', 'Mexican', 'Middle Eastern', 'Moroccan', 'North American', 'Spanish', 'Thai', 'Vietnamese', 'Other');

/**
 * Creates the XML content used to insert a new recipe.
 */
function buildInsertXML($name,$price,$description) {
	$result = "<?xml version='1.0'?>" . "\n";
// 	$result .= "<entry xmlns='http://www.w3.org/2005/Atom'" . " xmlns:g='http://base.google.com/ns/1.0'>" . "\n";
// 	$result .= "<category scheme='http://base.google.com/categories/itemtypes'" . " term='Products'/>" . "\n";
// 	$result .= "<title type='text'>" . 'pizza' . "</title>" . "\n";
// // 	$result .= "<g:cuisine>" . 'Chinese' . "</g:cuisine>" . "\n";
// 	$result .= "<g:item_type type='text'>Recipes</g:item_type>" . "\n";
// // 	$result .= "<g:cooking_time type='intUnit'>" . $_POST['time_val'] . " " . $_POST['time_units'] . "</g:cooking_time>" . "\n";
// // 	$result .= "<g:main_ingredient type='text'>" . $_POST['main_ingredient'] . "</g:main_ingredient>" . "\n";
// // 	$result .= "<g:serving_count type='number'>" . $_POST['serves'] . "</g:serving_count>" . "\n";
// 	$result .= "<content>" . 'whatever'. "</content>" . "\n";
// 	$result .= "</entry>" . "\n";
	
	$result .= "<entry xmlns='http://www.w3.org/2005/Atom' xmlns:g='http://base.google.com/ns/1.0'>
				<category scheme='http://base.google.com/categories/itemtypes' term='Products'/>
				<g:item_type type='text'>Products</g:item_type>
				<g:price type='floatUnit'> ".$price."</g:price>
				<title type='text'>".$name."</title>
				<content>".$description."</content>
			</entry>";

	return $result;
}

/**
 * Creates the XML content used to perform a batch delete.
 */
function buildBatchXML() {
	$counter = 0;
	
	$result =  '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$result .= '<feed xmlns="http://www.w3.org/2005/Atom"' . "\n";
	$result .= ' xmlns:g="http://base.google.com/ns/1.0"' . "\n";
	$result .= ' xmlns:batch="http://schemas.google.com/gdata/batch">' . "\n";
	foreach($_POST as $key => $value) {
		if(substr($key, 0, 5) == "link_") {
			$counter++;

			$result .= '<entry>' . "\n";
			$result .= '<id>' . $value . '</id>' . "\n";
			$result .= '<batch:operation type="delete"/>' . "\n";
			$result .= '<batch:id>' . $counter . '</batch:id>' . "\n";
			$result .= '</entry>' . "\n";
		}
	}
	$result .= '</feed>' . "\n";

	return $result;
}

/**
 * Exchanges the given single-use token for a session
 * token using AuthSubSessionToken, and returns the result.
 */
function exchangeToken($token) {
	$ch = curl_init();    /* Create a CURL handle. */

	curl_setopt($ch, CURLOPT_URL, "https://www.google.com/accounts/AuthSubSessionToken");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: AuthSub token="' . $token . '"'));

	$result = curl_exec($ch);  /* Execute the HTTP command. */
	curl_close($ch);

	$splitStr = split("=", $result);
	return trim($splitStr[1]);
}

/**
 * Performs a query for all of the user's items using the
 * items feed, then parses the resulting XML with the
 * startElement, endElement and characterData functions
 * (below).
 */
function getItems($token) {
	$ch = curl_init();    /* Create a CURL handle. */
	global $developerKey, $itemsFeedURL;

	curl_setopt($ch, CURLOPT_URL, $itemsFeedURL . "?");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml', 'Authorization: AuthSub token="' . trim($token) . '"', 'X-Google-Key: key=' . $developerKey));

	$result = curl_exec($ch);  /* Execute the HTTP command. */
	curl_close($ch);

	/* Parse the resulting XML. */
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "characterData"); 
	xml_parse($xml_parser, $result);
	xml_parser_free($xml_parser);
}

/**
 * Inserts a new recipe by performing an HTTP POST to the
 * items feed.
 */
function postItem($name,$price,$description, $token='') {
	$ch = curl_init();    /* Create a CURL handle. */
	global $developerKey, $itemsFeedURL;
	
	/* Set cURL options. */
	curl_setopt($ch, CURLOPT_URL, $itemsFeedURL);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: AuthSub token="' . $token . '"', 'X-Google-Key: key=' . $developerKey, 'Content-Type: application/atom+xml'));
	$xml=buildInsertXML($name,$price,$description);
// 	exit($xml);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	
	$result = curl_exec($ch);  /* Execute the HTTP request. */
	curl_close($ch);           /* Close the cURL handle. */

// 	exit($result);
	return $result;
}

/**
 * Updates an existing recipe by performing an HTTP PUT
 * on its feed URI, using the updated values a PUT data.
 */
function updateItem() {
	$ch = curl_init();    /* Create a CURL handle. */
	global $developerKey;
	
	/* Prepare the data for HTTP PUT. */
	$putString = buildInsertXML();
	$putData = tmpfile();
// 	exit("=======><pre>".var_dump($putData)."</pre>");
	fwrite($putData, $putString);
	fseek($putData, 0);
	
	/* Set cURL options. */
	curl_setopt($ch, CURLOPT_URL, $_POST['link']);
	curl_setopt($ch, CURLOPT_PUT, true);
	curl_setopt($ch, CURLOPT_INFILE, $putData);
	curl_setopt($ch, CURLOPT_INFILESIZE, strlen($putString));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: AuthSub token="' . $_POST['token'] . '"', 'X-Google-Key: key=' . $developerKey, 'Content-Type: application/atom+xml'));
	
	$result = curl_exec($ch);  /* Execute the HTTP request. */
	fclose($putData);          /* Close and delete the temp file. */
	curl_close($ch);           /* Close the cURL handle. */
	
	return $result;
}

/**
 * Deletes a recipe by performing an HTTP DELETE (a custom
 * cURL request) on its feed URI.
 */
function deleteItem() {
	$ch = curl_init();
	global $developerKey;

	/* Set cURL options. */
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_URL, $_POST['link']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: AuthSub token="' . $_POST['token'] . '"', 'X-Google-Key: key=' . $developerKey));
	
	$result = curl_exec($ch);  /* Execute the HTTP request. */
	curl_close($ch);           /* Close the cURL handle.    */
	
	return $result;
}

/**
 * Deletes all recipes by performing an HTTP POST to the
 * batch URI.
 */
function batchDelete() {
	$ch = curl_init();    /* Create a CURL handle. */
	global $developerKey, $itemsFeedURL;
	
	/* Set cURL options. */
	curl_setopt($ch, CURLOPT_URL, $itemsFeedURL . "/batch");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: AuthSub token="' . $_POST['token'] . '"', 'X-Google-Key: key=' . $developerKey, 'Content-Type: application/atom+xml'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, buildBatchXML());
	
	$result = curl_exec($ch);  /* Execute the HTTP request. */
	curl_close($ch);           /* Close the cURL handle.    */
	
	return $result;
}

/**
 * Callback function for XML start tags parsed by
 * xml_parse.
 */
function startElement($parser, $name, $attrs) {
	global $curElement, $foundEntry, $parsedEntries;
	
	$curElement = $name;
	if($curElement == "ENTRY") {
		$foundEntry = true;
		$parsedEntries[count($parsedEntries)] = array();
	} else if($foundEntry && $curElement == "LINK") {
		$parsedEntries[count($parsedEntries) - 1][$attrs["REL"]] = $attrs["HREF"];
	}
}

/**
 * Callback function for XML end tags parsed by
 * xml_parse.
 */
function endElement($parser, $name) {
	global $curElement, $foundEntry, $parsedEntries;
	if($name == "ENTRY") {
		$foundEntry = false;
	}
}

/**
 * Callback function for XML character data parsed by
 * xml_parse.
 */
function characterData($parser, $data) {
  global $curElement, $foundEntry, $parsedEntries;

  if($foundEntry) {
    $parsedEntries[count($parsedEntries) - 1][strtolower($curElement)] = $data;
  }
}

/**
 * We arrive here when the user first comes to the form. The first step is
 * to have them get a single-use token.
 */
function showIntroPage() {
	global $itemsFeedURL;
	
	$next_url  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	$redirect_url = 'https://www.google.com/accounts/AuthSubRequest?session=1';
	$redirect_url .= '&next=';
	$redirect_url .= urlencode($next_url);
	$redirect_url .= "&scope=";
	$redirect_url .= urlencode($itemsFeedURL);
	
	print '<html>' . "\n";
	print '<head><title>PHP 4 Demo: Google Base data API</title>' . "\n";
	print '<link rel="stylesheet" type="text/css" href="http://code.google.com/css/dev_docs.css">' . "\n";
	print '</head>' . "\n";
	print '<body><center>' . "\n";
	print '<table style="width:50%;">' . "\n";
	print '<tr>' . "\n";
	print '<th colspan="2" style="text-align:center;">PHP 4 Demo: Google Base data API</th>' . "\n";
	print '</tr>' . "\n";
	print '<tr><td>Before you get started, please <a href="' . $redirect_url . '">sign in</a> to your personal Google Base account.</td></tr>' . "\n";
	print '</table>' . "\n";
	print '</center></body></html>' . "\n";
}

/**
 * Prints the table of recipes the user has already entered
 * on the left-hand side of the page.
 */
function showRecipeListPane($token) {
  global $parsedEntries;

  print '<td style="width:50%; text-align:center; vertical-align:top">' . "\n";
  print '<table>' . "\n";
  print '<tr><th colspan="5" style="text-align:center">Recipes you have added</th></tr>' . "\n";

  getItems($token);

  if(count($parsedEntries) == 0) {
    print '<tr><td colspan="5" style="text-align:center"><i>(none)</i></td></tr>' . "\n";
  } else {
    print '<tr>' . "\n";
    print '<td style="text-align:center"><i>Name</i></td>' . "\n";
    print '<td style="text-align:center"><i>Cuisine</i></td>' . "\n";
    print '<td style="text-align:center"><i>Serves</i></td>' . "\n";
    print '<td colspan="2" style="text-align:center"><i>Actions</i></td>' . "\n";
    print '</tr>' . "\n";
    for($i = 0; $i < count($parsedEntries); $i++) {
      print '<tr>' . "\n";
      print '<td align="left" valign="top"><b><a href="' . 
        $parsedEntries[$i]['alternate'] . '">' .
        $parsedEntries[$i]['title'] . '</a></b></td>' . "\n";
      print '<td style="text-align:center;vertical-align:top">' .
        $parsedEntries[$i]['g:cuisine'] . '</td>' . "\n";
      print '<td style="text-align:center;vertical-align:top">' .
        $parsedEntries[$i]['g:serving_count'] . '</td>' . "\n";

      /* Create an Edit button for each existing recipe. */
      print '<td style="text-align:center;vertical-align:top">' . "\n";
      print '<form method="post" action="' . $_SERVER['PHP_SELF'] .
        '" style="margin-top:0;margin-bottom:0;">' . "\n";
      print '<input type="hidden" name="action" value="edit">' . "\n";
      print '<input type="hidden" name="token" value="' . $token . '">' . "\n";
      foreach ($parsedEntries[$i] as $key => $value) {
        print '<input type="hidden" name="' . $key . '" value="' .
          $value . '">' . "\n";
      }
      print '<input type="submit" value="Edit">' . "\n";
      print '</form>' . "\n";
      print '</td>' . "\n";

      /* Create a Delete button for each existing recipe. */
      print '<td style="text-align:center; vertical-align:top">' . "\n";
      print '<form method="post" action="' . $_SERVER['PHP_SELF'] .
        '" style="margin-top:0;margin-bottom:0;">' . "\n";
      print '<input type="hidden" name="action" value="delete">' . "\n";
      print '<input type="hidden" name="token" value="' . $token . '">' . "\n";
      print '<input type="hidden" name="link" value="' .
        $parsedEntries[$i]['id'] . '">' . "\n";
      print '<input type="submit" value="Delete">' . "\n";
      print '</form>' . "\n";
      print '</td>' . "\n";
      print '</tr>' . "\n";
    }
  }

  /* Create a "Delete all" button" to demonstrate batch requests. */
  print '<tr><td colspan="5" style="text-align:center">' . "\n";
  print '<form method="post" action="' . $_SERVER['PHP_SELF'] .
    '" style="margin-top:0;margin-bottom:0">' . "\n";
  print '<input type="hidden" name="action" value="delete_all">' . "\n";
  print '<input type="hidden" name="token" value="' . $token . '">' . "\n";
  for($i = 0; $i < count($parsedEntries); $i++) {
    print '<input type="hidden" name="link_' . $i . '" value="' .
      $parsedEntries[$i]['id'] . '">' . "\n";
  }
  print '<input type="submit" value="Delete All"';
  if(count($parsedEntries) == 0) {
    print ' disabled="true"';
  }
  print '></form></td></tr>' . "\n";
  print '</table>' . "\n";
  print '</td>' . "\n";
}

/**
 * Prints a small form allowing the user to insert a new
 * recipe.
 */
function showRecipeInsertPane($token) {
  global $cuisines;

  print '<td valign="top" width="50%">' . "\n";
  print '<table width="100%">' . "\n";
  print '<tr><th colspan="2" style="text-align:center">Insert a new recipe</th></tr>' . "\n";
  print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">' . "\n";
  print '<input type="hidden" name="action" value="insert">' . "\n";
  print '<input type="hidden" name="token" value="' . $token . '">' . "\n";
  print '<tr><td align="right">Title:</td>' . "\n";
  print '<td><input type="text" name="recipe_title" class="half">' .
    '</td></tr>' . "\n";
  print '<tr><td align="right">Main ingredient:</td>' . "\n";
  print '<td><input type="text" name="main_ingredient" class="half">' .
    '</td></tr>' . "\n";
  print '<tr><td align="right">Cuisine:</td>' . "\n";
  print '<td><select name="cuisine" class="half">' . "\n";

  foreach ($cuisines as $curCuisine) {
    print '<option value=' . $curCuisine . '>' . $curCuisine .
      '</option>' . "\n";
  }

  print '</select></td></tr>' . "\n";
  print '<tr><td align="right">Cooking Time:</td>' .
    '<td><input type="text" name="time_val" size=2 maxlength=2>&nbsp;' .
    '<select name="time_units"><option value="minutes">minutes</option>' .
    '<option value="hours">hours</option></select></td></tr>' . "\n";
  print '<tr><td align="right">Serves:</td>' . "\n";
  print '<td><input type="text" name="serves" size=2 maxlength=3></td></tr>' .
    "\n";
  print '<tr><td align="right">Recipe:</td>' . "\n";
  print '<td><textarea class="full" name="recipe_text"></textarea></td></tr>' .
    "\n";
  print '<td>&nbsp;</td><td><input type="submit" value="Submit"></td>' . "\n";
  print '</form></tr></table>' . "\n";
  print '</td>' . "\n";
}

/**
 * Shows a menu allowing the user to update an existing
 * recipe with the Base API update feature.
 */
function showEditMenu() {
  global $cuisines;
  $splitCookingTime = split(" ", $_POST['g:cooking_time']);

  print '<html>' . "\n";
  print '<head><title>PHP 4 Demo: Google Base data API</title>' . "\n";
  print '<link rel="stylesheet" type="text/css" href="http://code.google.com/css/dev_docs.css">' . "\n";
  print '</head>' . "\n";
  print '<body><center>' . "\n";
  print '<table style="width:50%">' . "\n";
  print '<tr><th colspan="2" style="text-align:center">Edit recipe:</th></tr>' . "\n";

  print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">' . "\n";
  print '<input type="hidden" name="action" value="update">' . "\n";
  print '<input type="hidden" name="link" value="' .
    $_POST['edit'] . '">' . "\n";
  print '<input type="hidden" name="token" value="' .
    $_POST['token'] . '">' . "\n";

  print '<tr><td align="right">Title:</td>' . "\n";
  print '<td><input type="text" name="recipe_title" class="half" value="'
    . $_POST['title'] . '"></td></tr>' . "\n";

  print '<tr><td align="right">Main ingredient:</td>' . "\n";
  print '<td><input type="text" name="main_ingredient" value="'
    . $_POST['g:main_ingredient'] . '" class="half"></td></tr>' . "\n";

  print '<tr><td align="right">Cuisine:</td>' . "\n";
  print '<td><select name="cuisine" class="half">' . "\n";

  foreach ($cuisines as $curCuisine) {
    print '<option value="' . $curCuisine . '"';
    if($curCuisine == $_POST['g:cuisine']) {
      print ' selected="selected"';
    }
    print '>' . $curCuisine . "</option>\n";
  }

  print '</select></td></tr>' . "\n";
  print '<tr><td align="right">Cooking Time:</td>' .
    '<td><input type="text" name="time_val" size=2 maxlength=2 value="' .
    $splitCookingTime[0] . '">&nbsp;' . "\n";
  print '<select name="time_units">' . "\n";
  if($splitCookingTime[1] == "minutes") {
    print '<option value="minutes" selected="selected">minutes</option>' .
      "\n";
    print '<option value="hours">hours</option>' . "\n";
  } else {
    print '<option value="minutes">minutes</option>' . "\n";
    print '<option value="hours" selected="selected">hours</option>' .
      "\n";
  }

  print '</select></td></tr>' . "\n";
  print '<tr><td align="right">Serves:</td>' . "\n";
  print '<td><input type="text" name="serves" value="' .
    $_POST['g:serving_count'] . '" size=2 maxlength=3></td></tr>' . "\n";

  print '<tr><td align="right">Recipe:</td>' . "\n";
  print '<td><textarea class="full" name="recipe_text">' .
    $_POST['content'] . '</textarea></td></tr>' . "\n";
  print '<td>&nbsp;</td><td><input type="submit" value="Update"></td>' . "\n";
  print '</form></tr></table>' . "\n";
  print '</body></html>' . "\n";
}

/**
 * Displays both the "List of current recipes" and
 * "Insert a new recipe" panels in a single table.
 */
function showMainMenu($tableTitle, $sessionToken) {
	print '<html>' . "\n";
	print '<head><title>PHP 4 Demo: Google Base data API</title>' . "\n";
	print '<link rel="stylesheet" type="text/css" href="http://code.google.com/css/dev_docs.css">' . "\n";
	print '</head>' . "\n";
	print '<body><center>' . "\n";
	print '<table style="width: 75%;text-align:center">' . "\n";
	print '<tr>' . "\n";
	print '<th colspan="2" style="text-align:center">PHP 4 Demo: Google Base data API' . "\n";
	print '</tr>' . "\n";
	print '<tr><td colspan="2" align="center">' . $tableTitle . '</td></tr>' . "\n";
	print '<tr>' . "\n";
	
	// Create the two sub-tables.
	showRecipeListPane($sessionToken);
	showRecipeInsertPane($sessionToken);
	
	// Add a "Sign out" link.
	print '<tr><th colspan="2" style="text-align: center">Or click here to' . ' <a href="http://www.google.com/accounts/Logout">sign out</a>' . ' of your Google account.</th></tr>' . "\n";
	
	// Close the master table.
	print '</table>' . "\n";
	print '</center></body></html>' . "\n";
}

/**
 * We arrive here after the user first authenticates and we get back
 * a single-use token.
 */
function showFirstAuthScreen() {
	$singleUseToken = $_GET['token'];
	$sessionToken = exchangeToken($singleUseToken);
	
	if(!$sessionToken) {
		showIntroPage();
	} else {
		$tableTitle = 'Here\'s your <b>single use token:</b> <code>' . $singleUseToken . '</code>' . "\n" . '<br>And here\'s the <b>session token:</b> <code>' . $sessionToken . '</code>';
		showMainMenu($tableTitle, $sessionToken);
	}
}

/**
 * Main logic. Take action based on the GET and POST
 * parameters, which reflect whether the user has
 * authenticated and which action they want to perform.
 */
// if(count($_GET) == 1 && array_key_exists('token', $_GET)) {
// 	  showFirstAuthScreen();
// } else {
// 	if(count($_POST) == 0) {
// 		showIntroPage();
// 	} else {
// 		if($_POST['action'] == 'insert') {
// 			if(postItem()) {
// 				showMainMenu('Recipe inserted!', $_POST['token']);
// 			} else {
// 				showMainMenu('Recipe insertion failed.', $_POST['token']);
// 			}
// 		} else if($_POST['action'] == 'delete') {
// 			if(deleteItem()) {
// 				showMainMenu('Recipe deleted.', $_POST['token']);
// 			} else {
// 				showMainMenu('Recipe deletion failed.', $_POST['token']);
// 			}
// 		} else if($_POST['action'] == 'delete_all') {
// 			if(batchDelete()) {
// 				showMainMenu('All recipes deleted.', $_POST['token']);
// 			} else {
// 				showMainMenu('Batch deletion failed.', $_POST['token']);
// 			}
// 		} else if($_POST['action'] == 'edit') {
// 			showEditMenu();
// 		} else if($_POST['action'] == 'update') {
// 			if(updateItem()) {
// 				showMainMenu('Recipe successfully updated.', $_POST['token']);
// 			} else {
// 				showMainMenu('Recipe update failed.', $_POST['token']);
// 			}
// 		} else {
// 			showIntroPage();
// 		}
// 	}
// }

?>
<?php
/*

UserFrosting Version: 0.2.0
By Alex Weissman
Copyright (c) 2014

Based on the UserCake user management system, v2.0.2.
Copyright (c) 2009-2012

UserFrosting, like UserCake, is 100% free and open-source.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the 'Software'), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

// Request method: GET

include('../models/db-settings.php');
include('../models/config.php');

set_error_handler('logAllErrors');

// User must be logged in
if (!isUserLoggedIn()){
  addAlert("danger", "You must be logged in to access this resource.");
  echo json_encode(array("errors" => 1, "successes" => 0));
  exit();
}

// GET Parameters: [user_id, group_id]
// If a user_id is specified, attempt to load group information for all groups associated with the specified user.
// If a group_id is specified, attempt to load information for the specified group.
// Otherwise, attempt to load all groups.
$validator = new Validator();
$user_id = $validator->optionalGetVar('user_id');
$group_id = $validator->optionalGetVar('group_id');

// Add alerts for any failed input validation
foreach ($validator->errors as $error){
  addAlert("danger", $error);
}

if ($user_id){
  // Special case to load groups for the logged in user
  if ($user_id == "0"){
    $user_id = $loggedInUser->user_id;
  }
  
  // Attempt to load group information for the specified user.
  if (!($results = loadUserGroups($user_id))){
      echo json_encode(array("errors" => 1, "successes" => 0));
      exit();
  }
} else if ($group_id){	
  // Attempt to load information for the specified group.
  if (!($results = loadGroup($group_id))){
      echo json_encode(array("errors" => 1, "successes" => 0));
      exit();
  }
} else {
  // Attempt to load information for all groups
  if (!($results = loadGroups())){
      echo json_encode(array("errors" => 1, "successes" => 0));
      exit();
  }
}

restore_error_handler();

echo json_encode($results);

?>
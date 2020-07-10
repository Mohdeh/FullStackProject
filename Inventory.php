<?php
header("Access-Control-Allow-Origin: *");
class Inventory {

  public function __construct(){
    // route to correct method for request handling
    if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['action'])){
      switch ($_GET["action"]){
        case "getAvailable":
          $this->getAvailable();
          break;
        case "getItemType":
          $this->getItemType();
          break;
        case "getDamageStatusKey":
          $this->getDamageStatusKey();
          break;
        case "getItemDataByName":
          isset($_GET['itemName']) ? $this->getItemDataByName($_GET['itemName']) :
                                     http_response_code(404);
          break;
        case "getUserEditPrivilege":
          $this->getUserEditPrivilege();
          break;
        default:
          http_response_code(404);
      }
    }
    else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action'])){
      switch ($_POST["action"]){
        case "addItem":
          $this->addItem();
          break;
        case "pushItemChange":
          $this->pushItemChange();
          break;
        case "deletePackage":
          isset($_POST['packageID']) ? $this->deletePackage($_POST['packageID']) : http_response_code(404);
          break;
        default:
          http_response_code(404);
      }
    }
  }

  public function getUserEditPrivilege(){
    // goes into returned json
    $PRIVILEGE_NAME = "editItemPrivilege";

    if(!isset($_SESSION['user_ID'])){
      echo json_encode(array("error" => "no user session"));
      exit;
    }
    try{
      if ($this->canUserEdit($_SESSION['user_ID'])){
        echo json_encode(array($PRIVILEGE_NAME => true, "id" => $_SESSION['user_ID']), JSON_PRETTY_PRINT);
        exit;
      }
      else{
        echo json_encode(array($PRIVILEGE_NAME => false), JSON_PRETTY_PRINT);
        exit;
      }
    }
    catch(Exception $e){
      $response = array($PRIVILEGE_NAME => false, "error" => "Exception getting user privileges");
      echo json_encode($response, JSON_PRETTY_PRINT);
      exit;
    }
  }


  private function canUserEdit($user_id){
    $EDIT_PRIVILEGE = 47;
    global $tbl_users_in_groups, $tbl_group_with_privileges, $tbl_users_with_privileges;

    $db = new DatabaseConnection();
    $escpd_user_id = $db->real_escape_string($user_id);
    $query = "SELECT groupID FROM $tbl_users_in_groups WHERE userID='$escpd_user_id'";
    $results = $db->query($query);
    $groupIds = array();

    while ($row = $results->fetch_assoc()){
      $groupIds[] = $row['groupID'];
    }

    foreach($groupIds as $id){
      $query = "SELECT * FROM $tbl_group_with_privileges WHERE group_ID=$id AND privilege_ID=$EDIT_PRIVILEGE";
      $results = $db->query($query);
      if ($db->num_rows($results) != 0){
        return true;
      }
    }

    $query = "SELECT * FROM $tbl_users_with_privileges WHERE user_ID=$escpd_user_id AND privilege_ID=$EDIT_PRIVILEGE";
    $results = $db->query($query);
    if ($db->num_rows($results) != 0){
      return true;
    }

    return false;
  }


  public function deletePackage($package_id){
    // if (!user_is_logged_in()){
    //   http_response_code(401);
    //   exit();
    // }
    global $tbl_package;
    try{
      $db = new DatabaseConnection();
    }
    catch(Exception $e){
      $response = array("success" => false, "error_text" => "Could not establish connection");
      echo json_encode($response);
      exit;
    }

    $clean_pkg_id = $db->real_escape_string($package_id);
    $query = "DELETE FROM $tbl_package WHERE ID=$clean_pkg_id";
    if (!$db->query($query)){
      $response = array("success" => false, "error_text" => "Database error deleting package");
      echo json_encode($response);
      exit;
    }

    $response = array("success" => true);
    echo json_encode($response);
    exit;

  }


  public function pushItemChange(){
    // if (!user_is_logged_in()){
    //   http_response_code(401);
    //   exit();
    // }
    $db = new DatabaseConnection();
    $changes = json_decode($_POST['changeData'], true);
    $rental_cost = $db->real_escape_string($changes['rentalRate']);
    $help_url = $db->real_escape_string($changes['helpUrl']);
    $item_id = $db->real_escape_string($changes['itemID']);
    $damageStatusUpdates = $changes['damageStatusUpdates'];
    $serials = $changes['serials'];
    $availabilityUpdates = $changes['availabilityUpdates'];
    $newPackages = $changes['newPackages'];

    // first update all the item data
    global $tbl_item;
    $query = "UPDATE $tbl_item SET rental_cost='$rental_cost', help_url='$help_url'
              WHERE ID='$item_id'";
    if (!$db->query($query)){
      $response = array("success" => false, "error_text" => "Could not update the item data");
      echo json_encode($response);
      exit;
    }

    // now parse arrays (JS objects) for package updates
    // serial updates:
    global $tbl_package;
    foreach($serials as $id => $new_serial){
      $id = $db->real_escape_string($id);
      $new_serial = $db->real_escape_string($new_serial);
      $query = "UPDATE $tbl_package SET serial='$new_serial' WHERE ID='$id'";
      if (!$db->query($query)){
        $response = array("success" => false, "error_text" => "Could not update serial for package id $id");
        echo json_encode($response);
        exit;
      }
    }

    // damage updates:
    foreach($damageStatusUpdates as $id => $new_dmg_stat){
      $id = $db->real_escape_string($id);
      $new_dmg_stat = $db->real_escape_string($new_dmg_stat);
      $query = "UPDATE $tbl_package SET damage_status='$new_dmg_stat' WHERE ID='$id'";
      if (!$db->query($query)){
        $response = array("success" => false, "error_text" => "Could not update serial for package id $id");
        echo json_encode($response);
        exit;
      }
    }

    // availability updates
    foreach($availabilityUpdates as $id => $new_avb){
      ;
    }

    // add new packages
    foreach($newPackages as $idx => $new_pkg){
      $pkg_fields = array("serialNumber" => $new_pkg['serial_number'],
                       "damageStatus" => $new_pkg['damage_status'],
                       "active" => "1",
                     );
      $this->addPackage($item_id, $pkg_fields, $new_pkg['pkg_number']);
    }

    $success_response = array("success" => true);
    echo json_encode($success_response);

  }


  public function getAvailable(){
    // if ( !user_is_logged_in() ){
    //     http_response_code(401);
    //     exit();
    // }
    global $tbl_available_inventory;
    global $tbl_current_checkouts, $tbl_current_reservations;
    global $tbl_package;
    global $tbl_damage_status;
    global $tbl_item;

    $query = "SELECT INV.*, P.damage_status AS status_ID, P.serial AS serial_number, DS.name AS status_text, I.contents AS contents
              FROM $tbl_available_inventory INV
              JOIN $tbl_package P ON P.ID=INV.package_ID
              JOIN $tbl_damage_status DS ON DS.ID=P.damage_status
              JOIN $tbl_item I ON INV.item_ID=I.ID
              WHERE INV.store_ID=1
              ORDER BY INV.item_name";
    $db = new DatabaseConnection();
    $results = $db->query($query);
    $data = array();
    while ($row = $results->fetch_assoc()){
      $data[] = $row;
    }
    $returning_JSON = array();
    $returning_JSON['all_items'] = $data;

    $checkouts_query = "SELECT package_ID FROM $tbl_current_checkouts";
    $results = $db->query($checkouts_query);
    $returning_JSON['checked_out_ids'] = array();
    while ($row = $results->fetch_assoc()){
      $returning_JSON['checked_out_ids'][] = $row['package_ID'];
    }

    $reservations_query = "SELECT package_ID FROM $tbl_current_reservations";
    $results = $db->query($reservations_query);
    $returning_JSON['reserved_ids'] = array();
    while ($row = $results->fetch_assoc()){
      $returning_JSON['reserved_ids'][] = $row['package_ID'];
    }

    echo json_encode($returning_JSON, JSON_PRETTY_PRINT);
  }


  private function get_file_extension($file_path){
    $file_type = mime_content_type($file_path);
    $extension = "";
    switch($file_type){
      case "image/jpeg":
        $extension = ".jpg";
        break;
      case "image/png":
        $extension = ".png";
        break;
      case "image/bmp":
        $extension = ".bmp";
        break;
    }
    return $extension;
  }


  public function addItem(){
    // if (!user_is_logged_in()){
    //   http_response_code(401);
    //   exit();
    // }
    global $tbl_item, $tbl_package;
    try{
      $db = new DatabaseConnection();
      $rentalPrice = $db->real_escape_string($_POST['rentalPrice']);
      $totalCost = $db->real_escape_string($_POST['totalCost']);
      $itemName = $db->real_escape_string($_POST['itemName']);
      $itemType = $db->real_escape_string($_POST['itemType']);
      $helpURL = $db->real_escape_string($_POST['helpURL']);
      $packages = json_decode($_POST['packages'], true);


      $lrg_upload_dir = __SITE_PATH . "/includes/images/reservation/pkg_lg/";
      $sml_upload_dir = __SITE_PATH . "/includes/images/reservation/pkg_sm/";

      $lrg_extension = $this->get_file_extension($_FILES['largeItemPic']['tmp_name']);
      $sml_extension = $this->get_file_extension($_FILES['smallItemPic']['tmp_name']);
    }
    catch(Exception $e){
      $response = array("success" => false, "error" => "Server Error Processing Form");
      echo json_encode($response);
      exit();
    }
    if ($lrg_extension == "" || $sml_extension == "" || $lrg_extension != $sml_extension){
      $response = array("success" => false, "error" => "Invalid File Type");
      echo json_encode($response);
      exit();
    }

    // $lrg_extension == $sml_extension so choice is arbitrary
    $uploadfile = $itemName . $lrg_extension;
    $uploadfile = str_replace(' ', '_', $uploadfile);

    if (move_uploaded_file($_FILES['largeItemPic']['tmp_name'], $lrg_upload_dir . $uploadfile)
        && move_uploaded_file($_FILES['smallItemPic']['tmp_name'], $sml_upload_dir . $uploadfile)){
      $response = array("success"     => true,
                        "upload"      => $uploadfile,
                        "rentalPrice" => $rentalPrice,
                        "totalCost"   => $totalCost,
                        "itemName"    => $itemName,
                        "itemType"    => $itemType);
    }
    else{
      $response = array("success" => false,
                        "error"   => "File upload error");
      echo json_encode($response);
      exit();
    }


    $query = "INSERT INTO $tbl_item
    (name, photo_url, help_url, total_value, rental_cost, type)
    VALUES ('$itemName', '$uploadfile','$helpURL', $totalCost, $rentalPrice, $itemType)";
    if(!$db->query($query)){
      $response = array("success" => false,
                        "error"   => "Database insertion error");
    }

    $itemID = $db->insert_id();
    $package_number = 1;
    foreach ($packages as $package){
      $this->addPackage($itemID, $package, $package_number);
      $package_number++;
    }

    echo json_encode($response);
  }


  private function addPackage($itemID, $pkg_fields, $pkg_number){
    global $tbl_package;
    $serial = isset($pkg_fields['serialNumber']) ? $pkg_fields['serialNumber'] : "";
    $damage_status = isset($pkg_fields['damageStatus']) ? $pkg_fields['damageStatus'] : "";
    $active = isset($pkg_fields['active']) ? $pkg_fields['active'] : "";

    $insert_query = "INSERT INTO $tbl_package
                     (item_ID, pkg_number, serial, damage_status, active)
                     VALUES ($itemID, '$pkg_number', '$serial', '$damage_status', '$active')";
    $db = new DatabaseConnection();
    return $db->query($insert_query);
  }


  public function getItemType(){
    // if (!user_is_logged_in()){
    //   http_response_code(401);
    //   exit();
    // }
    global $tbl_item_type;
    $query = "SELECT ID, name FROM $tbl_item_type";
    $db = new DatabaseConnection();
    $results = $db->query($query);
    $data = array();
    while ($row = $results->fetch_assoc()){
      $data[] = $row;
    }
    echo json_encode($data, JSON_PRETTY_PRINT);
  }


  public function getItemDataByName($item_name){
    // if (!user_is_logged_in()){
    //   http_response_code(401);
    //   exit();
    // }
    global $tbl_item, $tbl_package;
    $item_query = "SELECT * FROM $tbl_item WHERE name='$item_name'";
    $db = new DatabaseConnection();
    $results = $db->query($item_query);
    $data = array();
    while ($row = $results->fetch_assoc()){
      $data[] = $row;
    }

    $item_id = $data[0]['ID'];
    $package_query = "SELECT * FROM $tbl_package WHERE item_ID=$item_id";
    $results = $db->query($package_query);
    $packages = array();
    while ($row = $results->fetch_assoc()){
      $packages[] = $row;
    }
    $data[0]['packages'] = $packages;
    echo json_encode($data[0], JSON_PRETTY_PRINT);
  }


  public function getDamageStatusKey(){
    // if (!user_is_logged_in()){
    //   http_response_code(401);
    //   exit();
    // }
    // get key of status code to english status
    global $tbl_damage_status;
    $query = "SELECT ID, name FROM $tbl_damage_status";
    $db = new DatabaseConnection();
    $results = $db->query($query);
    $returning_JSON = array();
    while ($row = $results->fetch_assoc()){
      $returning_JSON['status_key'][] = $row;
    }
    echo json_encode($returning_JSON, JSON_PRETTY_PRINT);
  }

}

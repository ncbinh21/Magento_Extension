<?php
// authenticate user
$gs_pword = isset( $_POST['pword'] ) ? $_POST['pword'] : '';
$gs_uname = isset( $_POST['uname'] ) ? $_POST['uname'] : '';
$gs_isAuthenticated = 0;
//$env = (include '../app/etc/env.php');

if( $gs_uname != 'softstar-admin' || $gs_pword != 'UpscaleShipwright3VerticalWasp' )
{
    header('Location: index.php');
}
else
{
    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=salesDetails.txt");
    header("Pragma: no-cache");
    header("Expires: 0");
}
//$servername = $env['db']['connection']['default']['host'];
//$port = '3306';
//$username = $env['db']['connection']['default']['username'];
//$password = $env['db']['connection']['default']['password'];
//$dbname = $env['db']['connection']['default']['dbname'];
$servername = '127.0.0.1';
$port = '3306';
$username = 'softstar_mage2';
$password = '9ZcxRCTEcMvoq27rHw';
$dbname = 'softstar_mage2';

$pdoOptions = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

try{
    //prep dates
    $ls_startdate = (string)date( "Y-m-d", strtotime( $_REQUEST['startdate'] ) );
    $ls_enddate = (string)date( "Y-m-d", strtotime( $_REQUEST['enddate'] ) );

    $db = new PDO('mysql:host=' . $servername .';port=' . $port . ';dbname=' . $dbname . ';charset=utf8', $username, $password, $pdoOptions);

    $smtpAssociated = $db->prepare("SELECT  sfoi.item_id, sfoi.parent_item_id, sfoi.name 
									FROM sales_order_item sfoi			
							 WHERE sfoi.parent_item_id IS NULL
								AND sfoi.created_at > ?
								AND sfoi.created_at < ADDDATE(?, 1)");
    $smtpAssociatedName = $db->prepare("SELECT  sfoi.item_id, sfoi.parent_item_id, sfoi.name 
									FROM sales_order_item sfoi			
							 WHERE sfoi.parent_item_id IS NOT NULL
								AND sfoi.created_at > ?
								AND sfoi.created_at < ADDDATE(?, 1)");


    $smtpAssociated->execute(array($ls_startdate, $ls_enddate));
    $salesResultsAssociated = $smtpAssociated->fetchAll(PDO::FETCH_ASSOC);

    $smtpAssociatedName->execute(array($ls_startdate, $ls_enddate));
    $salesResultsAssociatedName = $smtpAssociatedName->fetchAll(PDO::FETCH_ASSOC);

    $db = new PDO('mysql:host=' . $servername .';port=' . $port . ';dbname=' . $dbname . ';charset=utf8', $username, $password, $pdoOptions);



    // get all sales details from the sales_flat tables
    $stmt = $db->prepare("SELECT   sf_order.increment_id				AS 'order_#'
								  ,sf_order.customer_firstname			AS FName
								  ,sf_order.customer_lastname			AS LName
								  ,sf_order.customer_email				AS Email
								  ,sf_order_addr.city					AS City
								  ,sf_order_addr.region					AS Region
                                  ,sf_order_addr.postcode   			AS ZIP
								  ,sf_order_addr.country_id				AS Country
								  ,sfoi_parent.item_id					AS IdItem
								  ,sfoi_parent.sku						AS SKU
								  ,sfoi_parent.name						AS Name
								  ,sfoi_parent.product_type				AS Product_type
								  ,sfoi_parent.qty_ordered				AS Quantity
								  ,sfoi_parent.qty_refunded				AS Quantity_refunded
								  ,sf_order.status						AS Status
								  ,sfoi_parent.created_at				AS Puchased_on
								  ,\"\"                                 AS Associated_Product_Names
								  ,sfoi_parent.row_total				AS Price
								  ,(CASE WHEN src.code IS NULL AND sfoi_parent.product_type = 'bundle'
										 THEN (SELECT GROUP_CONCAT(c.code SEPARATOR ', ')
											   FROM sales_order_item sfoi
											   LEFT OUTER JOIN salesrule_coupon c
												 ON sfoi.applied_rule_ids = c.rule_id
											   WHERE sfoi.parent_item_id = sfoi_parent.item_id)
										 ELSE src.code
								   END) 								AS Coupon_code
								  ,sfoi_parent.product_options   		AS Options
								  ,sf_order_addr.firstname              As FirstName
								  ,sf_order_addr.lastname               As LastName
							 FROM sales_order_item sfoi_parent
							 INNER JOIN sales_order sf_order
								 ON sfoi_parent.order_id = sf_order.entity_id
							 LEFT OUTER JOIN salesrule_coupon src
								 ON sfoi_parent.applied_rule_ids = src.rule_id
							 LEFT OUTER JOIN sales_order_address sf_order_addr
								 ON sf_order.billing_address_id = sf_order_addr.entity_id
							 WHERE sfoi_parent.parent_item_id IS NULL
								AND sfoi_parent.created_at > ?
								AND sfoi_parent.created_at < ADDDATE(?, 1)");

    //sales_flat_order > sales_order
    //sales_flat_order_item > sales_order_item
    //sales_flat_order_address > sales_order_address
    $stmt->execute(array($ls_startdate, $ls_enddate));
    $numOfColumns = $stmt->columnCount();
    $salesResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // build first row of CSV. Should be field names.
    $header = '';
//    die(print_r($numOfColumnsAssociated));
    for($i = 0; $i < $numOfColumns; $i++)
    {
        $col = $stmt->getColumnMeta($i);
        $header .= $col['name'] . "|";
    }
    $header = str_replace('Options|','',$header);
    $header = str_replace('FirstName|','',$header);
    $header = str_replace('LastName|','',$header);
    $header = str_replace('IdItem|','',$header);
    $data = "";
    // build CSV content (bar separated because of comma separated fields).
    while ( $sale = array_shift( $salesResults ) )
    {
        $line = '';
        foreach ($salesResultsAssociated as $key => $item) {
            $nameMerge = '';
            if($item['item_id'] == 401853) {
                $nameMerge;
            }
            foreach ($salesResultsAssociatedName as $keyName => $itemName) {
                if($itemName['parent_item_id'] == $item['item_id']) {
                    if($itemName['name']) {
                        if($nameMerge) {
                            $nameMerge = $nameMerge . ', ' . $itemName['name'];
                        } else {
                            $nameMerge = $itemName['name'];
                        }
                    }
                }
            }
            if($sale['IdItem'] == $item ['item_id']){
                $sale['Associated_Product_Names'] = $nameMerge;
                unset($salesResultsAssociated[$key]);
                break;
            }
        }
        if($sale['Product_type'] == 'configurable' || $sale['Product_type'] == 'simple') {
            if(is_serialized($sale['Options']))
            {
                $buyRequest = unserialize($sale['Options']);
            } else {
                $buyRequest = json_decode($sale['Options'], true);
            }
//			$buyRequest = unserialize($sale['Options']);
            $option_line = $sale['Associated_Product_Names'];

            if(isset($buyRequest['options'])) {
                $options = $buyRequest['options'];
                foreach($options as $option) {
                    if($option_line == '') {
                        $option_line = $option['value'];
                    } else {
                        $option_line .= ', '.$option['value'];
                    }

                }
            }
            $sale['Associated_Product_Names'] = $option_line;
        }

        foreach( $sale as $key => $value )
        {
            if($key == 'FName') {
                if(!$value && $sale['FirstName']) {
                    $value = $sale['FirstName'];
                }
            }
            if($key == 'LName') {
                if(!$value && $sale['FirstName']) {
                    $value = $sale['FirstName'];
                }
            }
            if($key == 'Options' || $key == 'FirstName' || $key == 'LastName' || $key == 'IdItem') {
                continue;
            }
            if ( ( !isset( $value ) ) || ( $value == "" ) )
            {
                $value = "|";
            }
            else
            {
                $value = str_replace( '"' , '""' , $value );
                $value = '"' . $value . '"' . "|";
            }

            $line .= $value;
        }
        $data .= trim( $line ) . "\n";
    }
    $data = str_replace( "\r" , "" , $data );

    if ( $data == "" )
    {
        $data = "(0) Records Found!\n";
    }

    // close the connection
    $db = null;
}
catch(Exception $ex){
    $data .= $ex->getMessage();
}
function is_serialized($value, &$result = null)
{
    // Bit of a give away this one
    if (!is_string($value))
    {
        return false;
    }
    // Serialized false, return true. unserialize() returns false on an
    // invalid string or it could return false if the string is serialized
    // false, eliminate that possibility.
    if ($value === 'b:0;')
    {
        $result = false;
        return true;
    }
    $length = strlen($value);
    $end    = '';
    switch ($value[0])
    {
        case 's':
            if ($value[$length - 2] !== '"')
            {
                return false;
            }
        case 'b':
        case 'i':
        case 'd':
            // This looks odd but it is quicker than isset()ing
            $end .= ';';
        case 'a':
        case 'O':
            $end .= '}';
            if ($value[1] !== ':')
            {
                return false;
            }
            switch ($value[2])
            {
                case 0:
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                    break;
                default:
                    return false;
            }
        case 'N':
            $end .= ';';
            if ($value[$length - 1] !== $end[0])
            {
                return false;
            }
            break;
        default:
            return false;
    }
    if (($result = @unserialize($value)) === false)
    {
        $result = null;
        return false;
    }
    return true;
}
print "$header\n$data";
<?php

// authenticate user
$gs_pword = isset( $_POST['pword'] ) ? $_POST['pword'] : '';
$gs_uname = isset( $_POST['uname'] ) ? $_POST['uname'] : '';
$gs_isAuthenticated = 0;
if( $gs_uname != 'softstar-admin' || $gs_pword != 'r7bli&13tiOO' )
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

$servername = 'localhost:3306';
$username = 'softstar_softsta';
$password = 't7in&wo3thH1';
$dbname = 'softstar_magento';

$connection = mysql_connect( $servername, $username, $password ) or die( mysql_error() );
mysql_select_db( $dbname, $connection ) or die( mysql_error() );

//prep dates
$ls_startdate = "'" . mysql_real_escape_string( date( "Y-m-d", strtotime( $_REQUEST['startdate'] ) ) ) . "'";
$ls_enddate = "'" . mysql_real_escape_string( date( "Y-m-d", strtotime( $_REQUEST['enddate'] ) ) ) . "'" ;

// get all sales details from the sales_flat tables
$ls_sql = "SELECT sf_order.increment_id					AS 'order_#'
				 ,sf_order.customer_firstname			AS FName
				 ,sf_order.customer_lastname			AS LName
				 ,sf_order.customer_email				AS Email
				 ,sf_order_addr.city					AS City
				 ,sf_order_addr.region					AS Region
				 ,sf_order_addr.country_id				AS Country
				 ,sfoi_parent.sku						AS SKU
				 ,sfoi_parent.name						AS Name
				 ,sfoi_parent.product_type				AS Product_type
			     ,sfoi_parent.qty_ordered				AS Quantity
			     ,sfoi_parent.qty_refunded				AS Quantity_refunded
			     ,sf_order.status						AS Status
			     ,sfoi_parent.created_at				AS Puchased_on
			     ,(SELECT GROUP_CONCAT(sfoi.name SEPARATOR ', ')
				   FROM sales_order_item sfoi
				   WHERE sfoi.parent_item_id = sfoi_parent.item_id) AS Order_Details
			     ,sfoi_parent.row_total					AS Price
				 ,(CASE WHEN src.code IS NULL AND sfoi_parent.product_type = 'bundle'
						THEN (SELECT GROUP_CONCAT(c.code SEPARATOR ', ')
							  FROM sales_order_item sfoi
							  LEFT OUTER JOIN salesrule_coupon c
								ON sfoi.applied_rule_ids = c.rule_id
							  WHERE sfoi.parent_item_id = sfoi_parent.item_id)
						ELSE src.code
				  END) AS Coupon_code
                 ,sfoi_parent.product_options   		AS Options
		   FROM sales_order_item sfoi_parent
		   INNER JOIN sales_order sf_order
				ON sfoi_parent.order_id = sf_order.entity_id
		   LEFT OUTER JOIN salesrule_coupon src
				ON sfoi_parent.applied_rule_ids = src.rule_id
		   LEFT OUTER JOIN sales_order_address sf_order_addr
				ON sf_order.billing_address_id = sf_order_addr.entity_id
		   WHERE sfoi_parent.parent_item_id IS NULL
				AND sfoi_parent.created_at > $ls_startdate
				AND sfoi_parent.created_at < ADDDATE($ls_enddate, 1)";
//sales_flat_order > sales_order
//sales_flat_order_item > sales_order_item
//sales_flat_order_address > sales_order_address
$lo_result = mysql_query( $ls_sql, $connection );
$fields = mysql_num_fields ( $lo_result )-1;

// build first row of CSV. Should be field names.
$header = '';
for ( $i = 0; $i < $fields; $i++ )
{
    $header .= mysql_field_name( $lo_result , $i ) . "|";
}

// build CSV content (bar separated because of comma separated fields).
while ( $sale = mysql_fetch_array( $lo_result, MYSQL_ASSOC ) )
{
	$line = '';
    
    if($sale['Product_type'] == 'configurable') {
        $buyRequest = unserialize($sale['Options']);
        $option_line = $sale['Order_Details'];
        if(isset($buyRequest['options'])) {
            $options = $buyRequest['options'];
            foreach($options as $option) {
                $option_line .= ', '.$option['value'];
            }
        }
        $sale['Order_Details'] = $option_line;
    }
    
    foreach( $sale as $key => $value )
    {                                          
        if($key == 'Options') {
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

print "$header\n$data";

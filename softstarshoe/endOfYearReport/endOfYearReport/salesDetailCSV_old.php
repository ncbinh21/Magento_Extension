<?php

// authenticate user
$gs_isAuthenticated = isset( $_POST['isAuthenticated'] ) ? $_POST['isAuthenticated'] : 0;
if( $gs_isAuthenticated != 1 )
{
	header('Location: index.php');
}
else
{
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=salesDetails.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
}

$servername = 'localhost:3306';
$username = 'softstar';
$password = 't7in&wo3thH1';
$dbname = 'softstar_magento';

$connection = mysql_connect( $servername, $username, $password ) or die( mysql_error() );
mysql_select_db( $dbname, $connection ) or die( mysql_error() );

//prep dates
$ls_startdate = "'" . mysql_real_escape_string( date( "Y-m-d", strtotime( $_REQUEST['startdate'] ) ) ) . "'";
$ls_enddate = "'" . mysql_real_escape_string( date( "Y-m-d", strtotime( $_REQUEST['enddate'] ) ) ) . "'" ;

// get all sales details from the sales_flat tables
$ls_sql = "SELECT sf_order.increment_id					AS 'order_#'
				 ,sfoi_parent.sku						AS SKU
				 ,sfoi_parent.name						AS Name
				 ,sfoi_parent.product_type				AS Product_type
			     ,sfoi_parent.qty_ordered				AS Quantity
			     ,sf_order.status						AS Status
			     ,sfoi_parent.created_at				AS Puchased_on
			     ,(SELECT GROUP_CONCAT(sfoi.name SEPARATOR ', ')
				   FROM sales_order_item sfoi
				   WHERE sfoi.parent_item_id = sfoi_parent.item_id) AS Associated_Product_Names
			     ,sfoi_parent.row_total					AS Price
		   FROM sales_order_item sfoi_parent
		   INNER JOIN sales_flat_order sf_order
				ON sfoi_parent.order_id = sf_order.entity_id
		   WHERE sfoi_parent.parent_item_id IS NULL
				AND sfoi_parent.created_at > $ls_startdate
				AND sfoi_parent.created_at < $ls_enddate";
//sales_flat_order > sales_order
//sales_flat_order_item > sales_order_item
//sales_flat_order_address > sales_order_address
$lo_result = mysql_query( $ls_sql, $connection );
$fields = mysql_num_fields ( $lo_result );

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
    foreach( $sale as $value )
    {                                          
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

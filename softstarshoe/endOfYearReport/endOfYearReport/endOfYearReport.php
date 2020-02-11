
<?php
$gs_startdate = isset( $_POST['startdate'] ) ? $_POST['startdate'] : date( 'm/d/Y', time() );
$gs_enddate = isset( $_POST['enddate'] ) ? $_POST['enddate'] : date( 'm/d/Y', time() );

// authenticate user
$gs_pword = isset( $_POST['pword'] ) ? $_POST['pword'] : '';
$gs_uname = isset( $_POST['uname'] ) ? $_POST['uname'] : '';
$gs_isAuthenticated = 0;
if( $gs_uname != 'softstar-admin' || $gs_pword != 'UpscaleShipwright3VerticalWasp' )
{
	header('Location: index.php');
}

?>

<html>
<head></head>
<body>

<h3>Soft Star Shoes</h3>
<h4>Sales Report</h4>
<p>Download an Excel file containing a bar separated list of sales details. Filter by date.</p>

<form action="/endOfYearReport/salesDetailCSV.php" method="post">
	<p>
		<label>Start Date
			<input name="startdate" type="text" value="<?php echo htmlentities( $gs_startdate ); ?>" size="10" maxlength="10" />
		</label>
		<label>End Date
			<input name="enddate" type="text" value="<?php echo htmlentities( $gs_enddate ); ?>" size="10" maxlength="10" />
		</label>
	</p>
	<input type="hidden" name="submitted" value="1" />
	
	<input type="hidden" name="uname" value="<?php echo htmlentities($gs_uname); ?>" />
	<input type="hidden" name="pword" value="<?php echo htmlentities($gs_pword); ?>" />
	<input type="submit" value="Next &gt;&gt;" />
</form>

</body>
</html>
<!doctype html>
<html lang="en">
	<head>
		<style>
			.tbl-colorful 
			{
				border-collapse: collapse ;
			}
			.tbl-colorful tr th, .tbl-colorful tr td
			{
				border: 1px solid #888;
			}
			.tbl-colorful tr th
			{
				background: #EEE;
			}
			body
			{
				font-family: tahoma, sans-serif;
			}
		</style>
	</head>
	<body>
		<table style="width: 100%;">
			<tr>
				<td width="30%"><img src="<?php echo ('assets/images/printlogo.png'); /*TODO: Server dependant Code*/ ?>" /></td>
				<td colspan="3" style="text-align: right">
					Centre A (A division of Alapatt Properties Pvt.Ltd) <br/>
					7th Floor, Alapatt Heritage Building, MG Road, Kochi - 682035 
				</td>
			</tr>
			<?php echo $contents; ?>
			
			<tr>
				<td colspan="4">
					<br/>
					<br/>
					Pan No - AAMCA5620R <br/> 
					Service Tax code - AAMCA5620RSD001 <br/>
					<br/>
					<b>Payment Method:</b> <br/>
					Cheque <br/>
					<span style="padding-left:0.5cm;">Payable to 'Alapatt Properties Pvt. Ltd'</span>  <br/>
					<br/>
					Direct Deposit to Bank:  <br/>
					<span style="padding-left:0.5cm;">Federal Bank</span> <br/>
					Branch: Ernakulam/Broadway <br/>
					IFSC: FDRL0001283 <br/>
					A/c Number:12830200018527 <br/>
					<br/>
					<br/>
					<div><b style="border: 1px solid #000; padding: 2px;">Note: Please make your payments in seven days to avoid late payment charges<b></div>
					<br/>
					<span style="font-size: 0.9em; text-align: left">
						This is a computer generated invoice, stamp and signature not required.
					</span>
				</td>
			</tr>
		</table>
	</body>
</html>
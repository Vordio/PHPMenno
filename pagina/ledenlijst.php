<?php

$sql = mysql_query("SELECT *,DATE_FORMAT(regdatum, '%d/%m %h:%i') AS datum FROM leden ORDER BY gebruikersnaam ASC");

echo "
	<table width='300'>
		<tr>
			<td><strong>Gebruikersnaam</strong></td>
			<td><strong>Lid sinds</strong></td>
			<td><strong>Profiel</strong></td>
		</tr>";

while($row = mysql_fetch_assoc($sql)) {
	$sql_profiel = mysql_query("SELECT profiel_id FROM profiel WHERE member_id='".$row['member_id']."'");
	if(mysql_num_rows($sql_profiel) == 1) {
		$profiel = "ja";
	}else{
		$profiel = "nee";
	}
	echo "
		<tr>
			<td><a href='?p=profiel&mid=".$row['member_id']."'>".stripslashes(substr($row['gebruikersnaam'],0,25))."</a></td>
			<td width='100'>".$row['datum']."</td>
			<td>".$profiel."</td>
		</tr>";
}
echo "</table>";
?>
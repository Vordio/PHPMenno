<?php
if(!isset($_SESSION['id'])) {
	echo "Je bent niet ingelogd, <a href='index.php'>Ga terug</a>.";
	die();
}
?>
<script type="text/javascript">
function addSmilie(code) {
	document.form1.bericht.value+=code;
	document.form1.bericht.focus();
}
</script>
<?php
include('class/smiley.class.php');

if(isset($_SESSION['id'])) {
	if(isset($_GET['a'])) {
	
		if($_GET['a'] == "toevoegen") {
			
			if(isset($_GET['cat']) && is_numeric($_GET['cat'])) {
				if(isset($_POST['toevoegen']) && !empty($_POST['titel']) && !empty($_POST['bericht'])) {
					$timeoutseconds = 120;
					$timestamp = time();
					$timeout = $timestamp-$timeoutseconds;
					mysql_query("DELETE FROM forum_timeout WHERE moment<$timeout AND ip='".$_SERVER['REMOTE_ADDR']."'");
					$check_time = mysql_query("SELECT * FROM forum_timeout WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
					if(mysql_num_rows($check_time) < 1) {
						$titel = mysql_real_escape_string(substr($_POST['titel'],0,255));
						$bericht = mysql_real_escape_string($_POST['bericht']);
						
						mysql_query("INSERT INTO forum_berichten (titel,bericht,member_id,categorie,datum,ip) VALUES ('".$titel."','".nl2br($bericht)."','".$_SESSION['id']."','".$_GET['cat']."',NOW(),'".$_SERVER['REMOTE_ADDR']."')")or die (mysql_error());
						$sql = mysql_query("SELECT bericht_id FROM forum_berichten WHERE titel='".$titel."' AND bericht='".$bericht."' AND datum=NOW()");
						$row = mysql_fetch_assoc($sql);
						
						echo "Je bericht is succesvol geplaats, <a href='?p=forum&cat=".$_GET['cat']."&b=".$row['bericht_id']."'>Je kan hem hier bekijken</a>";
						mysql_query("INSERT INTO forum_timeout (ip,moment,member_id) VALUES ('".$_SERVER['REMOTE_ADDR']."','".$timestamp."','".$_SESSION['id']."')")or die (mysql_error());
					}else{
						echo "Je hebt al gepost in de laatste 2 minuten.";
					}
				}else{
					?>
					<form name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>?p=forum&cat=<?php echo $_GET['cat']; ?>&a=toevoegen" method="post">
						<table style="border:1px solid #000000;" width="300" cellpadding="0" cellspacing="0">
							<tr>
								<td><br>&nbsp;Titel</td>
								<td><br><input type="text" width="200" name="titel" maxlength="255" /></td>
							</tr>
							<tr>
								<td colspan="2"><br>&nbsp;Bericht</td>
							</tr>
							<tr>
								<th colspan="2">
								<img src="wysiwyg/icons/bold.gif" alt="Bold" onclick="javascript:addSmilie('[b]je tekst[/b]')" />
								<img src="wysiwyg/icons/italics.gif" alt="Italics" onclick="javascript:addSmilie('[i]je tekst[/i]')" />
								<img src="wysiwyg/icons/underline.gif" alt="Underline" onclick="javascript:addSmilie('[u]je tekst[/u]')" />
								<img src="wysiwyg/icons/insert_picture_on.gif" alt="Image" onclick="javascript:addSmilie('[img]je link[/img]')" />
								<img src="wysiwyg/icons/insert_hyperlink.gif" alt="Link" onclick="javascript:addSmilie('[url]je tekst[/url]')" />
								<img src="wysiwyg/icons/strikethrough.gif" alt="Doorstreept" onclick="javascript:addSmilie('[s]je tekst[/s]')" />
								<img src="wysiwyg/icons/forecolor_on.gif" alt="Kleur" onclick="javascript:addSmilie('[color=green]je tekst [/color]')" />
								<img src="wysiwyg/icons/view_text.gif" alt="Tekst Grootte" onclick="javascript:addSmilie('[size=3]je tekst [/size]')" />
								<img src="wysiwyg/icons/view_source.gif" alt="Code" onclick="javascript:addSmilie('[code]je code [/code]')" />
								<br><textarea style="width:270px;height:200px;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; border:1px solid #000000;" name="bericht"></textarea></th>
							</tr>
							<tr>
								<th colspan="2"><br><input type="submit" name="toevoegen" value="Toevoegen"></th>
							</tr>
							<tr>
								<th colspan="2"><br /><?php echo Smileys(); ?></th>
							</tr>
						</table>
					</form>
					<?php
				}
			}else{
				echo "Er is geen categorie id ingevult of hij is fout.";
			}
		}elseif($_GET['a'] == "aanpassen") {
			if(isset($_GET['cat']) && isset($_GET['b']) && is_numeric($_GET['b'])) {
				$sql = mysql_query("SELECT * FROM forum_berichten WHERE bericht_id='".$_GET['b']."'");
				$row = mysql_fetch_assoc($sql);
				if($row['member_id'] == $_SESSION['id']) {
					if(isset($_POST['aanpassen']) && !empty($_POST['titel']) && !empty($_POST['bericht'])) {
						$titel = mysql_real_escape_string(substr($_POST['titel'],0,255));
						$bericht = mysql_real_escape_string($_POST['bericht']);
						
						mysql_query("UPDATE forum_berichten SET titel='".$titel."', bericht='".nl2br($bericht)."' WHERE bericht_id='".$_GET['b']."'");
						echo "Je forum bericht is succesvol aangepast, <a href='?p=forum&cat=".$_GET['cat']."&b=".$_GET['b']."'>Bekijk je bericht hier</a>.";
					}else{
						?>
						<form name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>?p=forum&cat=<?php echo $_GET['cat']; ?>&b=<?php echo $_GET['b']; ?>&a=aanpassen" method="post">
							<table style="border:1px solid #000000;" width="300" cellpadding="0" cellspacing="0">
								<tr>
									<td><br>&nbsp;Titel</td>
									<td><br><input value="<?php echo stripslashes($row['titel']); ?>" type="text" width="200" name="titel" maxlength="255" /></td>
								</tr>
								<tr>
									<td colspan="2"><br>&nbsp;Bericht</td>
								</tr>
								<tr>
									<th colspan="2">
								<img src="wysiwyg/icons/bold.gif" alt="Bold" onclick="javascript:addSmilie('[b]je tekst[/b]')" />
								<img src="wysiwyg/icons/italics.gif" alt="Italics" onclick="javascript:addSmilie('[i]je tekst[/i]')" />
								<img src="wysiwyg/icons/underline.gif" alt="Underline" onclick="javascript:addSmilie('[u]je tekst[/u]')" />
								<img src="wysiwyg/icons/insert_picture_on.gif" alt="Image" onclick="javascript:addSmilie('[img]je link[/img]')" />
								<img src="wysiwyg/icons/insert_hyperlink.gif" alt="Link" onclick="javascript:addSmilie('[url]je tekst[/url]')" />
								<img src="wysiwyg/icons/strikethrough.gif" alt="Doorstreept" onclick="javascript:addSmilie('[s]je tekst[/s]')" />
								<img src="wysiwyg/icons/forecolor_on.gif" alt="Kleur" onclick="javascript:addSmilie('[color=green]je tekst [/color]')" />
								<img src="wysiwyg/icons/view_text.gif" alt="Tekst Grootte" onclick="javascript:addSmilie('[size=3]je tekst [/size]')" />
								<img src="wysiwyg/icons/view_source.gif" alt="Code" onclick="javascript:addSmilie('[code]je code [/code]')" />
								<br><textarea style="width:270px;height:200px;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; border:1px solid #000000;" name="bericht"><?php echo nl2br(stripslashes(addSmiley(ubb($row['bericht'])))); ?></textarea></th>
								</tr>
								<tr>
									<th colspan="2"><br><input type="submit" name="aanpassen" value="Aanpassen"></th>
								</tr>
								<tr>
									<th colspan="2"><br /><?php echo Smileys(); ?></th>
								</tr>
							</table>
						</form>
						<?php
					}
				}else{
					echo "Dit bericht is niet door jou gepost";
				}
			}else{
				echo "Er is geen categorie of bericht opgegeven.";
			}
		/////// hier een nieuwe $_GET['a']  neer zetten!!
		}elseif($_GET['a'] == "reactie") {
			if(isset($_GET['cat']) && isset($_GET['b']) && is_numeric($_GET['cat']) && is_numeric($_GET['b'])) {
				if(isset($_POST['reageren']) && !empty($_POST['bericht'])) {
					$timeoutseconds = 120;
					$timestamp = time();
					$timeout = $timestamp-$timeoutseconds;
					mysql_query("DELETE FROM forum_timeout WHERE moment<$timeout AND ip='".$_SERVER['REMOTE_ADDR']."'");
					$check_time = mysql_query("SELECT * FROM forum_timeout WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
					if(mysql_num_rows($check_time) < 1) {
						$bericht = mysql_real_escape_string($_POST['bericht']);
						mysql_query("INSERT INTO forum_reacties (member_id,bericht,bericht_id,categorie_id,ip) VALUES
							('".$_SESSION['id']."','".$bericht."','".$_GET['b']."','".$_GET['cat']."','".$_SERVER['REMOTE_ADDR']."')");
						echo "Je reactie is toegevoegd, Wacht a.u.b. een paar seconden <br />of ga <a href='?p=forum&cat=".$_GET['cat']."&b=".$_GET['b']."'>meteen</a> door.";
						mysql_query("INSERT INTO forum_timeout (ip,moment,member_id) VALUES ('".$_SERVER['REMOTE_ADDR']."','".$timestamp."','".$_SESSION['id']."')")or die (mysql_error());
					}else{
						echo "Je hebt al gepost in de laatste 2 minuten.";
					}
				}else{
					?>
					<form name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>?p=forum&cat=<?php echo $_GET['cat']; ?>&b=<?php echo $_GET['b']; ?>&a=reactie" method="post">
						<table width="300" style="border: 1px solid #000000" cellpadding="0" cellspacing="0">
							<tr>
								<th>Bericht</th>
							</tr>
							<tr>
								<td><a href='?p=forum&cat=<?php echo $_GET['cat']; ?>&b=<?php echo $_GET['b']; ?>'>Ga terug</a> |<br /></td>
							</tr>
							<tr>
								<th>
								<img src="wysiwyg/icons/bold.gif" alt="Bold" onclick="javascript:addSmilie('[b]je tekst[/b]')" />
								<img src="wysiwyg/icons/italics.gif" alt="Italics" onclick="javascript:addSmilie('[i]je tekst[/i]')" />
								<img src="wysiwyg/icons/underline.gif" alt="Underline" onclick="javascript:addSmilie('[u]je tekst[/u]')" />
								<img src="wysiwyg/icons/insert_picture_on.gif" alt="Image" onclick="javascript:addSmilie('[img]je link[/img]')" />
								<img src="wysiwyg/icons/insert_hyperlink.gif" alt="Link" onclick="javascript:addSmilie('[url]je tekst[/url]')" />
								<img src="wysiwyg/icons/strikethrough.gif" alt="Doorstreept" onclick="javascript:addSmilie('[s]je tekst[/s]')" />
								<img src="wysiwyg/icons/forecolor_on.gif" alt="Kleur" onclick="javascript:addSmilie('[color=green]je tekst [/color]')" />
								<img src="wysiwyg/icons/view_text.gif" alt="Tekst Grootte" onclick="javascript:addSmilie('[size=3]je tekst [/size]')" />
								<img src="wysiwyg/icons/view_source.gif" alt="Code" onclick="javascript:addSmilie('[code]je code [/code]')" /><br />
								<textarea name="bericht" style="width:275px; height:200px;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; border:1px solid #000000;"></textarea></th>
							</tr>
							<tr>
								<th><input type="submit" name="reageren" value="Reageren" /></th>
							</tr>
							<tr>
								<th><br /><?php echo Smileys(); ?></th>
							</tr>
						</table>
					</form>
					<?php
				}
			}else{
				echo "Er is geen geldige categorie of bericht opgegeven.";
			}
		}else{
			echo "Er is geen actie opgegeven.";
		}
	}elseif(isset($_GET['cat']) && is_numeric($_GET['cat']) && isset($_GET['b']) && is_numeric($_GET['b'])) {
		$sql = mysql_query("SELECT * FROM forum_berichten WHERE categorie='".$_GET['cat']."' AND bericht_id='".$_GET['b']."'");
		$row = mysql_fetch_assoc($sql);
		$sql_l = mysql_query("SELECT gebruikersnaam,avatar,rang,member_id FROM leden WHERE member_id='".$row['member_id']."'");
		$row_l = mysql_fetch_assoc($sql_l);
		
		$sql_posts = mysql_query("SELECT bericht_id FROM forum_berichten WHERE member_id='".$row['member_id']."'");
		$sql_posts1 = mysql_query("SELECT reactie_id FROM forum_reacties WHERE member_id='".$row['member_id']."'");
		$b_posts = mysql_num_rows($sql_posts);
		$r_posts = mysql_num_rows($sql_posts1);
		$posts = $b_posts + $r_posts;
		
		?>
		<table style="border-top:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000; padding-left: 10px;" width="300" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2"><h3><?php echo $row['titel']; ?></h3></td>
			</tr>
			<tr>
				<td style="border-bottom:1px solid #000000;" colspan="2"><br /><a href='?p=forum&cat=<?php echo $_GET['cat']; ?>'>Ga terug</a> | <a href="?p=forum&cat=<?php echo $_GET['cat']; ?>&a=toevoegen">Bericht Toevoegen</a> | <a href='?p=forum&cat=<?php echo $_GET['cat']; ?>&b=<?php echo $_GET['b']; ?>&a=reactie'>Reactie Toevoegen</a><br /><br /></td>
			</tr>
			<tr>
				<td width="90" style="padding-left:5px;" valign="top"><strong><a href="?p=profiel&mid=<?php echo $row_l['member_id']; ?>"><?php echo $row_l['gebruikersnaam']; ?></a></strong>
					<br /><?php echo $posts; ?> Posts<br />
					<strong><?php echo $row_l['rang']; ?></strong>
				<?php 
				if($row_l['avatar'] == "") {
					echo "<br /><img width='80' height='80' src='images/noavatar.gif' />";
				}else{
					echo "<br /><img src='".$row_l['avatar']."' />";
				}					
				?></td>
				<td valign="top" style="padding-right:5px;" width="310"><br /><?php echo wordwrap(stripslashes(nl2br(addSmiley(ubb($row['bericht'])))),40,"\n",1); ?><br /><br /></td>
			</tr>
		</table>
		
		<?php
		$sql_r = mysql_query("SELECT * FROM forum_reacties WHERE categorie_id='".$_GET['cat']."' AND bericht_id='".$_GET['b']."'");
		while($row_r = mysql_fetch_assoc($sql_r)) {
			$sql_leden = mysql_query("SELECT gebruikersnaam,avatar,rang,member_id FROM leden WHERE member_id='".$row_r['member_id']."'");
			$row_leden = mysql_fetch_assoc($sql_leden);
			$sql_posts = mysql_query("SELECT bericht_id FROM forum_berichten WHERE member_id='".$row_r['member_id']."'");
			$sql_posts1 = mysql_query("SELECT reactie_id FROM forum_reacties WHERE member_id='".$row_r['member_id']."'");
			$b_posts = mysql_num_rows($sql_posts);
			$r_posts = mysql_num_rows($sql_posts1);
			$posts = $b_posts + $r_posts;
			?>
			<table width="300" style="border-top:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000; padding-left: 10px;" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="90"><strong><a href="?p=profiel&mid=<?php echo $row_leden['member_id']; ?>"><?php echo $row_leden['gebruikersnaam']; ?></a></strong>
					<br /><?php echo $posts; ?> Posts<br />
					<strong><?php echo $row_leden['rang']; ?></strong>
					<?php 
					if($row_leden['avatar'] == "") {
						echo "<br /><img width='80' height='80' src=' images/noavatar.gif' />";
					}else{
						echo "<br /><img src='".$row_leden['avatar']."' />";
					}					
					?></td>
					<td valign="top" width="310"><?php echo wordwrap(stripslashes(nl2br(addSmiley(ubb($row_r['bericht'])))),40,"\n",1); ?></td>
				</tr>
			</table>
			<?php
		}
		?>

		<form name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>?p=forum&cat=<?php echo $_GET['cat']; ?>&b=<?php echo $_GET['b']; ?>&a=reactie" method="post">
			<table width="300" style="border: 1px solid #000000" cellpadding="0" cellspacing="0">
				<tr>
					<th><br />Bericht</th>
				</tr>
				<tr>
					<th>
						<img src="wysiwyg/icons/bold.gif" alt="Bold" onclick="javascript:addSmilie('[b]je tekst[/b]')" />
						<img src="wysiwyg/icons/italics.gif" alt="Italics" onclick="javascript:addSmilie('[i]je tekst[/i]')" />
						<img src="wysiwyg/icons/underline.gif" alt="Underline" onclick="javascript:addSmilie('[u]je tekst[/u]')" />
						<img src="wysiwyg/icons/insert_picture_on.gif" alt="Image" onclick="javascript:addSmilie('[img]je link[/img]')" />
						<img src="wysiwyg/icons/insert_hyperlink.gif" alt="Link" onclick="javascript:addSmilie('[url]je tekst[/url]')" />
						<img src="wysiwyg/icons/strikethrough.gif" alt="Doorstreept" onclick="javascript:addSmilie('[s]je tekst[/s]')" />
						<img src="wysiwyg/icons/forecolor_on.gif" alt="Kleur" onclick="javascript:addSmilie('[color=green]je tekst [/color]')" />
						<img src="wysiwyg/icons/view_text.gif" alt="Tekst Grootte" onclick="javascript:addSmilie('[size=3]je tekst [/size]')" />
						<img src="wysiwyg/icons/view_source.gif" alt="Code" onclick="javascript:addSmilie('[code]je code [/code]')" />
						<br />
						<textarea name="bericht" style="width:275px; font-family:Verdana, Arial, Helvetica, sans-serif; 
						font-size:12px; height:200px; border:1px solid #000000;"></textarea>
					</th>
				</tr>
				<tr>
					<th><input type="submit" name="reageren" value="Reageren" /></th>
				</tr>
				<tr>
					<th colspan="2"><br /><?php echo Smileys(); ?></th>
				</tr>
			</table>
		</form>
		<?php
	}elseif(isset($_GET['cat'])) {
		?>
		<table style="border:1px solid #000000;" width="300" cellpadding="0" cellspacing="0">
			<?php
			$sql = mysql_query("SELECT * FROM forum_berichten WHERE categorie='".$_GET['cat']."'");
			$sql_c = mysql_query("SELECT * FROM forum_categorie WHERE categorie_id='".$_GET['cat']."'");
			$row_c = mysql_fetch_assoc($sql_c);
			?>
			<tr>
				<th colspan="2"><?php echo $row_c['titel']; ?></th>
			</tr>
			<tr>
				<td colspan="2"><br /><a href='?p=forum'>Ga terug naar forum</a> | <a href="?p=forum&cat=<?php echo $_GET['cat']; ?>&a=toevoegen">Bericht Toevoegen</a> |<br /><br /></td>
			</tr>
			<?php
			while($row = mysql_fetch_assoc($sql)) {
				$sql_l = mysql_query("SELECT gebruikersnaam FROM leden WHERE member_id='".$row['member_id']."'");
				$row_l = mysql_fetch_assoc($sql_l);
				$sql_aantal_r = mysql_query("SELECT reactie_id FROM forum_reacties WHERE categorie_id='".$row['categorie']."' AND bericht_id='".$row['bericht_id']."'");
				$aantal_r = mysql_num_rows($sql_aantal_r);
				?>
				<tr>
					<td style="border-top:1px solid #000000;" height="25" width="60%"><a href='?p=forum&cat=<?php echo $_GET['cat']; ?>&b=<?php echo $row['bericht_id']; ?>'><?php echo $row['titel']; ?></a></td>
					<td style="border-top:1px solid #000000;" width="30%">
					Gepost: <strong><?php echo $row_l['gebruikersnaam']; ?></strong><br />
					<strong><?php echo $aantal_r; ?></strong> Reacties</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}else{
		?>
		<table style="border:1px solid #000000;" width="300" cellpadding="0" cellspacing="0">
			<?php
			$sql = mysql_query("SELECT * FROM forum_categorie");
			while($row = mysql_fetch_assoc($sql)) {
				$sql_aantal_b = mysql_query("SELECT bericht_id FROM forum_berichten WHERE categorie='".$row['categorie_id']."'");
				$sql_aantal_r = mysql_query("SELECT reactie_id FROM forum_reacties WHERE categorie_id='".$row['categorie_id']."'");
				$aantal_b = mysql_num_rows($sql_aantal_b);
				$aantal_r = mysql_num_rows($sql_aantal_r);
				?>
				<tr>
					<td colspan="2"><a href='?p=forum&cat=<?php echo $row['categorie_id']; ?>'><?php echo $row['titel']; ?></a></td>
				</tr>
				<tr>
					<td width="80%"><?php echo $row['uitleg']; ?></td>
					<td width="20%">
					<strong><?php echo $aantal_b; ?></strong> Berichten<br />
					<strong><?php echo $aantal_r; ?></strong> Reacties<br />
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
}else{
	echo "Je bent niet ingelogd, log <a href='inloggen.php'>Hier</a> even in of maak <a href='registreren.php'>Hier</a> even een account aan.";
}

?>
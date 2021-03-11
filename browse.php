<?php
if (!defined('WC_BASE')) define('WC_BASE', dirname(__FILE__));
$ref=WC_BASE."/index.php";
if ($ref!=$_SERVER['SCRIPT_FILENAME']){
	header("Location: index.php");
	exit();
}
?>
<!-- ############################## Start browse.php ###################################### -->
<tr>
	<td width="10">&nbsp;</td>

	<td valign="top">
		<h3>
			<?php print _("Browse domains");?>
		</h3>
		<?php
		if (isset($_GET['row_pos'])) $_SESSION['domain_row_pos'] = $_GET['row_pos'];
//		$_SESSION['domain_row_pos'] = $row_pos;
		if (isset($_GET['orderby'])) {
			if ($_SESSION['domain_orderby'] == $_GET['orderby']) {
				if ($_SESSION['domain_orderby_desc']=="asc") {
					$_SESSION['domain_orderby_desc'] = "desc";
				} else {
					$_SESSION['domain_orderby_desc'] = "asc";
				}
			} else {
				$_SESSION['domain_orderby_desc'] = "asc";
			}
			$_SESSION['domain_orderby'] = $_GET['orderby'];
		}
//		$_SESSION['domain_orderby'] = $orderby;
?>
<!-- 		<table border="1" width="98%"> -->
				<?php

				if (! isset($_SESSION['allowed_domains'])) {
					#$query = "SELECT * FROM domain ORDER BY domain_name";
					$query = "SELECT * FROM domain ORDER BY ".$_SESSION['domain_orderby']." ".$_SESSION['domain_orderby_desc'];
				} else {
					$domains = '';
					foreach ($_SESSION['allowed_domains'] as $allowed_domain) {
						$domains .= $allowed_domain."' OR domain_name='";
					}
					$query = "SELECT * FROM domain WHERE domain_name='$domains' ORDER BY ".$_SESSION['domain_orderby']." ".$_SESSION['domain_orderby_desc'];
				}

				$result = $handle->query($query);
				if (DB::isError($result)) {
					die (_("Database error (browse 50)"));
				}
//				$result = $handle->limitQuery($query,$row_pos,$_SESSION['maxdisplay']);
				$cnt    = $result->numRows($result);

				print _("Total Domains:")." ".$cnt;
				print "<br>"._("Displaying from position:")." ".($_SESSION['domain_row_pos']+1);
				
				?>
<!-- 		</table> -->
                <table cellspacing="2" cellpadding="0">
                        <tr>
				<?php 
				if ($_SESSION['admintype']==0){
					?>
	                                <td class="navi">
        	                                <a class="navilink" href="index.php?action=newdomain&domain=new"><?php print _("Add new domain");?></a>
                	                </td>
					<td width="20">&nbsp;</td>
                                <?php
				}
				
				$prev = $_SESSION['domain_row_pos'] - $_SESSION['maxdisplay'];
				if ($prev < 0 ) $prev = 0;
				$next = $_SESSION['domain_row_pos'] + $_SESSION['maxdisplay'];
				$last = $cnt - $_SESSION['maxdisplay'];

                                if ($_SESSION['domain_row_pos'] <= 0){
					print "<td class=\"navi\"><a class=\"navilink\">"._("First entry") ."</a></td>";		
					print "<td class=\"navi\"><a class=\"navilink\">"._("Previous entries")."</a></td>";
                                } else {
					print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=browse&row_pos=0\">"._("First entry") ."</a></td>";		
					print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=browse&row_pos=$prev\">"._("Previous entries") ."</a></td>";		
                                }

				if ($next>=$cnt){
					print "<td class=\"navi\"><a>"._("Next entries")."</a></td>";
					print "<td class=\"navi\"><a class=\"navilink\">"._("Last entry") ."</a></td>";
				} else {
					print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=browse&row_pos=$next\">". _("Next entries")."</a></td>";
					print "<td class=\"navi\"><a class=\"navilink\" href=\"index.php?action=browse&row_pos=$last\">"._("Last entry") ."</a></td>";
				}
                                ?>

                        </tr>
                 </table> 
		<table width="99%">


                       <tbody>
                                <tr>
					<?php
						print ($_SESSION['admintype']==0)?"<th colspan=\"4\">":"<th colspan=\"2\">";
						print _("action");
					?>
                                        </th>

                                        <th>
                                                <!-- <?php print _("domainname");?> -->
						<?php
						print "<a class=\"th_a\" href=\"index.php?action=browse&orderby=domain_name\">"._("domainname")."</a>";
						if ($_SESSION['domain_orderby'] == 'domain_name') print "<img src=\"images/arrow_sort_".$_SESSION['domain_orderby_desc'].".png\" border=\"0\">";
						?>
                                        </th>

                                        <?php
                                        if (! $DOMAIN_AS_PREFIX){
                                                ?>
                                                <th>
						<?php print "<a class=\"th_a\" href=\"index.php?action=browse&orderby=prefix\">"._("prefix")."</a>";
						if ($_SESSION['domain_orderby'] == 'prefix') print "<img src=\"images/arrow_sort_".$_SESSION['domain_orderby_desc'].".png\" border=\"0\">";
?>
                                                </th>
                                                <?php
                                        }
                                        ?>

                                        <th>
						<?php print "<a class=\"th_a\" href=\"index.php?action=browse&orderby=maxaccounts\">"._("max Accounts")."</a>";
						if ($_SESSION['domain_orderby'] == 'maxaccounts') print "<img src=\"images/arrow_sort_".$_SESSION['domain_orderby_desc'].".png\" border=\"0\">";
?>
                                        </th>

                                        <th>
                                                <!-- <?php print _("Domain quota");?> -->
						<?php print "<a class=\"th_a\" href=\"index.php?action=browse&orderby=domainquota\">"._("Domain quota")."</a>";
						if ($_SESSION['domain_orderby'] == 'domainquota') print "<img src=\"images/arrow_sort_".$_SESSION['domain_orderby_desc'].".png\" border=\"0\">";
?>
                                        </th>

                                        <th>
                                                <!-- <?php print _("default quota per user");?> -->
						<?php print "<a class=\"th_a\" href=\"index.php?action=browse&orderby=quota\">"._("default quota per user")."</a>";
						if ($_SESSION['domain_orderby'] == 'quota') print "<img src=\"images/arrow_sort_".$_SESSION['domain_orderby_desc'].".png\" border=\"0\">";
?>
                                        </th>
                                </tr>


<?php
				

				for ($c=$_SESSION['domain_row_pos']; $c < (($next>$cnt)?($cnt):($next)); $c++){
					if ($c%2==0){
						$cssrow="row1";
					} else {
						$cssrow="row2";
					}

					$row = $result->fetchRow(DB_FETCHMODE_ASSOC,$c);

					?>
					<tr class="<?php echo $cssrow;?>">
						<?php
						if ($_SESSION['admintype']==0) {
							$_cols = array(
								'editdomain'	=> _("Edit Domain"),
								'deletedomain'	=> _("Delete Domain"),
							);
							foreach ($_cols as $_action => $_txt){
							?>
								<td>
								<?php
								printf ('<a href="index.php?action=%s&amp;domain=%s">%s</a>',
									$_action, $row['domain_name'], $_txt);
								?>
								</td>
							<?php
							}
						}
						?>
						<td>
							<?php
							if ($row['transport'] == 'cyrus') {
								echo "<a href=\"index.php?action=accounts&domain=".$row['domain_name']."\">";
								print _("accounts");
								echo "</a>";
							} else {
								print _("accounts");
							}
							?>
						</td>
						
						<td>
							<?php
							if ($row['transport'] == 'cyrus') {
								echo "<a href=\"index.php?action=aliases&domain=".$row['domain_name']."\">";
								print _("Aliases");
								echo "</a>";
							} else {
								print _("Aliases");
							}
							?>
						</td>

						<td>
							<?php echo $row['domain_name'];?>
						</td>

							<?php
							if (! $DOMAIN_AS_PREFIX){
								# Print the prefix
								echo "<td>";
								echo $row['prefix'];
								echo "</td>";
							}
							?>
						<td align="right">
							<!-- Max Account -->
							<?php
							echo $row['maxaccounts'];
							?>
						</td>
						
						<td align="right">
							<!--  Max Domain Quota -->
							<?php
							if (! $row['domainquota'] == 0) {
								echo $row['domainquota'];
							} else {
								print _("Quota not set");
							}
							?>
						</td>
						
						<td align="right">
							<!-- Default Account Quota -->
							<?php
							echo $row['quota'];
							?>
						</td>
					</tr>
					<?php
				} // End of for
				?>
			</tbody>
		</table>
			<p>&nbsp;

<!-- ############################### End browse.php ############################################# -->


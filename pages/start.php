<?php
error_reporting(E_ALL);

require_once "_classes/subs.php";
require_once "_classes/core.php";
require_once "_classes/share.php";
require_once "_classes/server.php";
require_once "_classes/uploads.php";
require_once "_classes/downloads.php";
require_once "_classes/icons.php";

$lang = $_SESSION['language']['START'];

$Servers = new Server();
$core = new Core;
$icon_img = new Icons();

//Info holen
$statusbar_xml=$core->command("xml","modified.xml?filter=informations");
$temp=array_keys($statusbar_xml['INFORMATION']);
$information=&$statusbar_xml['INFORMATION'][$temp[0]];
$temp2=array_keys($statusbar_xml['NETWORKINFO']);
$netinfo=&$statusbar_xml['NETWORKINFO'][$temp2[0]];

//Serververbindung?
$serverconnection=$_SESSION['language']['SERVER']['STATUS_NOT_CONNECTED'];
if($netinfo['CONNECTEDWITHSERVERID']>=0){
	$serverconnection=$_SESSION['language']['SERVER']['STATUS_CONNECTED'];
}else{
	if($netinfo['TRYCONNECTTOSERVER']>=0)
		$serverconnection=$_SESSION['language']['SERVER']['STATUS_CONNECTING'];
}

$_SESSION['phpaj']['core_source_ip']=$netinfo['IP'];
if(empty($_SESSION['phpaj']['core_source_port'])){
	$settings=$core->command("xml","settings.xml");
	$_SESSION['phpaj']['core_source_port']=$settings['PORT']['VALUES']['CDATA'];
}

if(isset($information['MAXUPLOADPOSITIONS'])){
	$_SESSION['cache']['UPLOADS']['phpaj_MAXUPLOADPOSITIONS']=
		$information['MAXUPLOADPOSITIONS'];
}

//Warnungen
	$warnungen=array();
	if($Servers->netstats['firewalled']==='true'){
		$warnungen[] = $_SESSION['language']['SERVER']['FIREWALLED'];
	}

	if(!empty($warnungen)){
		echo "<h2>".$lang['WARNINGS']."</h2>";
		echo "<div style=\"margin-left:0.5cm;background-color:#FF0000;\">";
		foreach($warnungen as $a) {
		    echo "<img src=\"../style/" .$_SESSION['server_warning_icon']."\" alt=\"[!]\" />".$a."<br />";
        }
		echo "</div>";
	}
?>

<div class="row clearfix">
<?php echo $warnung; ?>
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
		<!-- aktueller Server -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                <div class="panel-heading bg-success"><i class="fa fa-server"></i> aktueller Server</div>
                <div class="panel-body">
                	<h3><?php echo $Servers->netstats['servername']; ?></h3>
					<?php
                      //server welcome msg
						if(!empty($Servers->netstats['welcome'])){
							echo $Servers->netstats['welcome'];
						} 
					?>
                </div>
            </div>
        </div>
        <!-- DOWNLOADS UND UPLOADS COUNTER -->
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="info-box infobox-type-1">
                            <div class="icon bg-primary"><i class="material-icons">cloud_download</i></div>
                            <div class="content">
                                <div class="text">aktive Downloads</div>
                                <div class="number count-to" data-from="0" data-to="245" data-speed="1000" data-fresh-interval="20"><?php
                    	$Downloadlist = new Downloads();
                    	$counddown = $downloadids=$Downloadlist->ids("name",$subdir);
                    	$Downloadlist->refresh_cache();
						$subdircounter=0;
						//alle downloads zeigen
						foreach(array_keys($Downloadlist->subdirs) as $subdir){
							$subdircounter++;
							$downloadids=$Downloadlist->ids("",$subdir); //ids der downloads sortiert holen
						}
						echo count($downloadids); 
					?></div>
                            </div>
                        </div>
                    </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="info-box infobox-type-1">
                            <div class="icon bg-warning"><i class="material-icons">cloud_upload</i></div>
                            <div class="content">
                                <div class="text">aktive Uploads</div>
                                <div class="number count-to" data-from="0" data-to="245" data-speed="1000" data-fresh-interval="20"><?php
                    	$Downloadlist = new Downloads();
                    	$counddown = $downloadids=$Downloadlist->ids("name",$subdir);
                    	$Downloadlist->refresh_cache();
						$subdircounter=0;
						//alle downloads zeigen
						foreach(array_keys($Downloadlist->subdirs) as $subdir){
							$subdircounter++;
							$downloadids=$Downloadlist->ids("",$subdir); //ids der downloads sortiert holen
						}
						echo count($downloadids); 
					?></div>
                            </div>
                        </div>
                    </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="info-box infobox-type-1">
                            <div class="icon bg-success"><i class="material-icons">folder_open</i></div>
                            <?php
                   
                	$Sharelist = new Share();
					if($_ENV['GUI_SHOW_SHARE']) {
		    			$Sharelist->refresh_cache(30);
    				}
					if(!empty($_SESSION['phpaj']['share_LASTTIMESTAMP'])){
						$share_anzahl=0;
						$share_groesse=0;
						foreach(array_keys($Sharelist->cache['SHARES']['VALUES']['SHARE']) as $a){
							$share_anzahl++;
							$share_groesse+=$Sharelist->cache['SHARES']['VALUES']['SHARE'][$a]['SIZE'];
						}
					echo'<div class="content">
                    		<div class="text">'.number_format($share_anzahl).' geteilte Dateien</div>
                    		<div class="number">
                    			'.sizeformat($share_groesse).'
						 </div>';
					}else{
					}
				?>
                            </div>
                        </div>
                    </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="info-box infobox-type-1">
                            <div class="icon bg-primary"><i class="material-icons">star</i></div>
                            <div class="content">
                                <div class="text">Credits</div>
                                <div class="number count-to" data-from="0" data-to="245" data-speed="1000" data-fresh-interval="20"><?php
                                
							if($information['CREDITS'] <= 0){
								$creditcolor = " class='text-danger'";
							}else{
								$creditcolor =" class='ext-success'"; 
							}
							echo"<span".$creditcolor." >".sizeformat($information['CREDITS'])."</span>"; 
						?>
		</div>
                            </div>
                        </div>
                    </div>
    <div class="">hllo test</div>        
                    
	</div>
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
		<!--INFOS EINHOLEN -->
		<?php	
			$coreinfo = $Servers->core->getcoreversion();
			$coresubversions=explode(".",$_SESSION['cache']['STATUSBAR']['VERSION']);
			$info = $Servers->info();
		?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                <div class="panel-heading bg-success"><i class="material-icons">public</i> Core & Netzwerk Info</div>
                <div class="panel-body">
        			<table class="table">
                		<tbody>		
							<tr>
								<td>Server Zeit</td>
								<td>
									<?php
										echo $Servers->time();
										echo "<a href=\"javascript: window.location.href='".$_SERVER['PHP_SELF']."\"><i class=\"bi bi-arrow-clockwise\"></i></a>";
									?>
								</td>
                			<tr>
                    			<td>phpGUI Version</td>
                    			<td><?php echo"".PHP_GUI_VERSION.versions_checker(PHP_GUI_VERSION).""; ?></td>
                			</tr>
							<tr>
                    			<td>Core Version</td>
                    			<td><?php echo $coreinfo['VERSION']; ?></td>
                			</tr>
							<tr>
                    			<td>Betriebssystem</td>
                    			<td><?php echo $icon_img->os["2"]." ".$coreinfo['SYSTEM']; ?></td>
                			</tr>
							<tr>
                    			<td>verbunden seit</td>
                    			<td>
                    				<?php 
										$srv_timediff=$Servers->netstats['timeconnected'];
										$srv_timediff=sprintf("%dh %dmin %ds",$srv_timediff/3600,($srv_timediff%3600)/60,$srv_timediff%60);
										echo $srv_timediff;
									?>
								</td>
                			</tr>
							<tr>
                    			<td>offene Verbindungen</td>
                    			<td><?php echo"".$info['OPENCONNECTIONS'].""; ?></td>
                			</tr>
							<tr>
                    			<td>Anzahl User sharen</td>
                    			<td><?php echo"".$Servers->netstats['users'].""; ?></td>
                			</tr>
							<tr>
                    			<td>gesamte Dateien</td>
                    			<td><?php echo"".number_format($Servers->netstats['filecount'])." - ".sizeformat($Servers->netstats['filesize']).""; ?></td>
                			</tr>
                		</tbody>
            		</table>
                </div>
            </div>
            <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                <div class="panel-heading"><i class="material-icons">compare_arrows</i> Connection Info</div>
                <div class="panel-body">
        			<table class="table">
                		<tbody>		
							<tr>
								<td>Bytes in</td>
                    			<td><?php echo"".sizeformat($information['SESSIONDOWNLOAD']).""; ?></td>
                			</tr>
							<tr>
                    			<td>Bytes out</td>
                    			<td><?php echo"".sizeformat($information['SESSIONUPLOAD']).""; ?></td>
                			</tr>
							<tr>
                				<td>Downloadspeed</td>
                    			<td><?php echo"".sizeformat($information['DOWNLOADSPEED']).""; ?></td>
                			</tr>
							<tr>
                				<td>Uploadspeed</td>
                    			<td><?php echo"".sizeformat($information['UPLOADSPEED']).""; ?></td>
                			</tr>
                		</tbody>
            		</table>
                </div>
            </div>
        </div>
	</div>
</div>	
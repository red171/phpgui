<?php
session_start();
require_once "_classes/subs.php";
require_once "_classes/core.php";
require_once "_classes/share.php";

$core = new Core();
$share = new Share();
$template = new template();
echo "<script>
function ShowFiles(dir){
	dir=encodeURIComponent(dir);
	var sharelist=window.open('index.php?site=sharefiles&dir='+dir+'&".SID."','ajsharelist',
		'width=720,height=500,left=10,top=10,dependent=yes,scrollbars=yes');
}

function do_setsubs(name, newsub){
	name=encodeURIComponent(name);
	window.location.href='".$_SERVER['PHP_SELF']."?site=shares&setsubs='+name+'&newsub='+newsub;
}

function delshare(name){
	name=encodeURIComponent(name);
	window.location.href='".$_SERVER['PHP_SELF']."?".SID."&share_del='+name;
}

function newshare(){
	var name=encodeURIComponent(document.mainform.new_share.value);
	var subs=document.mainform.new_subs.checked ? 1 : 0;
	window.location.href='".$_SERVER['PHP_SELF']."index.php?site=shares&new_share='+name+'&new_subs='+subs;
}

function share_export(){
    window.location.href = 'index.php?site=shareexport';
}

function select_dir(){
	var dirlist=window.open(
		'directory.php?returninput=mainform.new_share.value&amp;".SID."',
		'Dirlist','width=400,height=350,left=10,top=10,dependent=yes,scrollbars=no');
	dirlist.focus();
}
</script>
</head>
<body>";

//einstellungen fuer unterverzeichnis aendern
if(!empty($_GET['setsubs'])){
	$share->changesub($_GET['setsubs'], $_GET['newsub']);
	$template->alert("info", "Inprogress", "Share Daten werden neu eingelesen!");
}

//verzeichnis aus share nehmen
if(!empty($_GET['share_del'])){
	$share->del_share($_GET['share_del']);
}

//verzeichnis sharen
if(!empty($_GET['new_share'])){
	$share->add_share($_GET['new_share'], $_GET['new_subs']);
	$message("Shareverzichnisse werden neu eingelesen und an den Server weitergeleitet.");
}

echo "<form action=\"\" name=\"mainform\">";

echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                        	<div class="panel-heading bg-success"><i class="fa fa-folder"></i> Freigegebene Verzeichnisse</div>
                            <div class="panel-body">
                            	<div class="table-responsive">
									<table class="table table-striped">
										<thead>
                							<tr>
                    							<th scope="col">#</th>
                								<th scope="col">Name</th>
                    							<th width="1" scope="col"><i class="fa fa-info-circle text-info"></i></th>
                    							<th scope="col">Größe</th>
                    							<th width="3" scope="col">Prio</th>
                    						</tr>
            							</thead>
                						<tbody>';
//auch temp-verzeichnis anzeigen (für dateien die gerade geladen werden)
echo'<tr>
		<td width="1"><i class="fa fa-folder"></i></td>
		<td colspan="4">
			<a href="index.php?site=sharefiles&dir='.addslashes(htmlspecialchars($share->get_temp())).'" aria-current="true">
            '.htmlspecialchars($share->get_temp()).'
            </a>
        </td>
    </tr>';
$sharedirs=$share->get_shared_dirs(1);

//freigegebene verzeichnisse anzeigen
foreach($sharedirs as $a){
	$cur_share=$share->get_shared_dir($a);
	//verzeichnisname -> link zu den einzelnen dateien
	echo'<tr>
		<td width="1"><i class="fa fa-folder"></i></td>
		<td colspan="4">
			<a href="index.php?site=sharefiles&dir='.addslashes(htmlspecialchars($cur_share["NAME"])).'" aria-current="true">
            '.htmlspecialchars($cur_share["NAME"]).'
            </a>
        </td>
    </tr>';
}
echo'
    </tbody>
    </table>
    </div></div>
          </div>
          </div>';
//Neues verzeichnis freigeben
//echo "\n<tr><td>".$_SESSION['language']['SHARE']['NEW']
//	.": <input name=\"new_share\" size=\"60\" />";
//echo " <input type=\"button\" value=\"...\" onclick=\"select_dir();\" /></td>";
//echo "<td><input type=\"checkbox\" name=\"new_subs\" value=\"1\" "
//	."checked=\"checked\" /></td><td><input type=\"button\" value=\""
//	.$_SESSION['language']['SHARE']['ADD']."\" "
//	."onclick=\"newshare()\"/></td></tr>\n";
//echo "</table>";
//
//echo "<br />\n";
echo "<div align=\"center\"><table><tr>\n";
echo "<td><input type=\"button\" onclick=\"do_setsubs('*sharecheck',0);\" value='"
	.$_SESSION['language']['SHARE']['SHARECHECK']."' /></td>";
echo "<td><input type=\"button\" onclick=\"share_export()\" value='"
	.$_SESSION['language']['SHARE']['EXPORTLIST']."' /></td>\n";
echo "</tr></table></div>\n";
echo "</form>\n";

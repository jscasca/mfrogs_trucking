<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$multipleQuery =false;
$queryTickets ="
SELECT
	*
FROM
	project
	JOIN address using (addressId)
 ";

switch($_GET['projectActive']){
		case '0':
		if(!$multipleQuery){$queryTickets.="WHERE ";}
		$queryTickets.="projectInactive=0 ";
		$multipleQuery=true;
		break;
		case '1':
		if(!$multipleQuery){$queryTickets.="WHERE ";}
		$queryTickets.="projectInactive=1 ";
		$multipleQuery=true;
		break;
}
if($_GET['projectName']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="projectName like '".$_GET['projectName']."%' ";
	$multipleQuery=true;
}
if($_GET['projectId']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="projectId like '%".$_GET['projectId']."%' ";
}
if($_GET['addressLine']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="addressLine1 like '".$_GET['addressLine']."%' ";
	$multipleQuery=true;
}
if($_GET['addressCity']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="addressCity like '".$_GET['addressCity']."%' ";
}
$queryTickets.="
ORDER BY 
	projectName
";
//echo $queryTickets;
$tbody="";
$tbody.= "
							<tbody>
						";
				$projects = mysql_query($queryTickets,$conexion);
				$numBrokers=mysql_num_rows($projects);
					$first =true;
					$class = " class='bg' ";
				if($numBrokers>0)
				{
						while($project = mysql_fetch_assoc($projects)){
						if($first){
							$tbody.= "<tr>";
							$tbody.= "<td $class>".$project['projectId']."</td>";
							$tbody.= "<td id='project".$project['projectId']."' $class>".$project['projectName']."</td>";
							if($class=="")$class=" class='bg' ";
							else $class="";
						}else{
							$tbody.= "<td $class>".$project['projectId']."</td>";
							$tbody.= "<td id='project".$project['projectId']."' $class>".$project['projectName']."</td>";
							$tbody.= "</tr>";
						}
						$first = !$first;
					}
					if(!$first){$tbody.= "<td colspan='2' $class></td></tr>";}
				}
			$tbody.= "</tbody>";
$jsondata['table']=$tbody;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>


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
	fakeproject
	JOIN address using (addressId)
 ";

if($_GET['fakeprojectName']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="fakeprojectName like '".$_GET['fakeprojectName']."%' ";
	$multipleQuery=true;
}
if($_GET['fakeprojectId']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="fakeprojectId like '%".$_GET['fakeprojectId']."%' ";
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
	fakeprojectName
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
							$tbody.= "<td $class>".$project['fakeprojectId']."</td>";
							$tbody.= "<td id='fakeproject".$project['fakeprojectId']."' $class>".$project['fakeprojectName']."</td>";
							if($class=="")$class=" class='bg' ";
							else $class="";
						}else{
							$tbody.= "<td $class>".$project['fakeprojectId']."</td>";
							$tbody.= "<td id='fakeproject".$project['fakeprojectId']."' $class>".$project['fakeprojectName']."</td>";
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


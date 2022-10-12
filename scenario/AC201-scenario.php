require_once dirname(__FILE__) . '/../php/core.inc.php';
$plugin = plugin::byId('zigbee');

##############################
#  Don't touch values below  #
##############################

$MACADDRESS="";

//Trigger command
$t_cmde=cmd::cmdToHumanReadable($scenario->getRealTrigger());

//extract usefull names
$objet=strtok($t_cmde,"#[]");
$equipmt=strtok("#[]");
$cmde=strtok("#[]");
$AC201_OBJECT_NAME="[$objet][$equipmt]";


// Parse Zigbee devices to get $AC201_OBJECT_NAME MAC address
$eqLogics = eqLogic::byType($plugin->getId());
$devices = array();
foreach ($eqLogics as $eqLogic) {
    $eqLogicArray = array();
    $eqLogicArray['HumanNameFull'] = $eqLogic->getHumanName(true);
    $eqLogicArray['HumanName'] = $eqLogic->getHumanName();
    $eqLogicArray['id'] = $eqLogic->getId();
    $eqLogicArray['instance'] = $eqLogic->getConfiguration('instance', 1);
    $eqLogicArray['img'] = 'plugins/zigbee/core/config/devices/' . zigbee::getImgFilePath($eqLogic->getConfiguration('device'));
    $devices[$eqLogic->getLogicalId()] = $eqLogicArray;
}
foreach($devices as $key => $value)
{
    if ($value["HumanName"] == $AC201_OBJECT_NAME)
    {
        $MACADDRESS=$key;
    }
}

if ($MACADDRESS == ""){
    die ("Error: unknown Zigbee Object: $AC201_OBJECT_NAME");
}

// Thermostat modes
$mode_thermostat["Arrêt"]=0;
$mode_thermostat["Auto"]=1;
$mode_thermostat["Climatisation"]=3;
$mode_thermostat["Chauffage"]=4;
$mode_thermostat["Air"]=7;
$mode_thermostat["Dry"]=8;

//Fan mode
$mode_fan["FanMode.Low"]=1;
$mode_fan["FanMode.Medium"]=2;
$mode_fan["FanMode.High"]=3;
$mode_fan["FanMode.Auto"]=5;

switch ($cmde) {
  case "Mode Thermostat":
        $value=$mode_thermostat[cmd::byString('#'.$AC201_OBJECT_NAME.'[Mode Thermostat]#')->execCmd()];
        $data='{"ieee":"'.$MACADDRESS.'","cmd":[{"endpoint":1,"cluster_type":"in","cluster":65453,"command":"write_ac_status_request","args":[0,1,1,'.$value.'],"await":0}]}';
        break;
    case "Consigne Chaud":
        $consigne_chaud=cmd::byString('#'.$AC201_OBJECT_NAME.'[Consigne Chaud]#')->execCmd()*100;
        $data='{"ieee":"'.$MACADDRESS.'","cmd":[{"endpoint":1,"cluster_type":"in","cluster":65453,"command":"write_ac_status_request","args":[0,1,2,'.$consigne_chaud.'],"await":0}]}';
        break;
    case "Consigne Froid":
        $consigne_froid=cmd::byString('#'.$AC201_OBJECT_NAME.'[Consigne Froid]#')->execCmd()*100;
        $data='{"ieee":"'.$MACADDRESS.'","cmd":[{"endpoint":1,"cluster_type":"in","cluster":65453,"command":"write_ac_status_request","args":[0,1,3,'.$consigne_froid.'],"await":0}]}';
        break;
    case "Mode Ventilation":
        $value=$mode_fan[cmd::byString('#'.$AC201_OBJECT_NAME.'[Mode Ventilation]#')->execCmd()];
        $data='{"ieee":"'.$MACADDRESS.'","cmd":[{"endpoint":1,"cluster_type":"in","cluster":65453,"command":"write_ac_status_request","args":[0,1,4,'.$value.'],"await":0}]}';
        break;
  default:
		$scenario->setLog( "unknown trigger:".$cmde );
    break;
}

try {
  $data = json_decode($data);
  $result = zigbee::request(init('instance', 1), '/device/command', $data, init('type', 'PUT'));
  log::add('zigbee', 'debug', json_encode($result, true));
  echo json_encode(array('state' => 'ok', 'result' => $result));
} catch (Exception $e) {
   $scenario->setLog(json_encode(array('state' => 'error', 'result' => $e->getMessage())));
}
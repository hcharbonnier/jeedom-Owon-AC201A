<?php
require_once dirname(__FILE__) . "/../../../../../../core/php/core.inc.php";
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  echo '<div class="alert alert-danger div_alert">';
  echo translate::exec('401 - Accès non autorisé');
  echo '</div>';
  die();
}
?>
<form class="form-horizontal">
  <fieldset>
    <legend><i class="fas fa-key"></i> {{Set IR code}}</legend>
    <div class="form-group">
      <label class="col-sm-3 control-label">{{Mémoire}}</label>
      <div class="col-sm-2">
        <select id="in_owonDeviceId" class="form-control">
          <?php
          for ($i = 1; $i <= 5; $i++) { 
            echo '<option value="' . $i . '">' . $i . '</option>';
          }
          ?>
        </select>
      </div>
      <div class="col-sm-2">
        <input id="in_owonIRCode" type="number" class="form-control" placeholder="{{Code}}" />
      </div>
      <div class="col-sm-2">
      <a class="btn btn-success" id="bt_owonValidateIRCode">{{Send IR Code}}</a>
      </div>
    </div>
  </fieldset>
  <fieldset>
    <legend><i class="fas fa-key"></i> {{Set Heat temp}}</legend>
    <div class="form-group">
      <div class="col-sm-2">
        <input id="in_owonIRCodesettest" type="number" class="form-control" placeholder="{{Code}}" />
      </div>
      <div class="col-sm-2">
      <a class="btn btn-success" id="bt_owonValidateIRCodetest">{{Send Heat temp}}</a>
      </div>
    </div>
  </fieldset>
  <fieldset>
    <legend><i class="fas fa-key"></i> {{Read IR code}}</legend>
    <div class="form-group">
      <div class="col-sm-2">
      <a class="btn btn-success" id="bt_owonReadIRCode">{{Read IR Code}}</a>
      </div>
    </div>
  </fieldset>
  <fieldset>
    <legend><i class="fas fa-key"></i> {{Auto Learn IR code}}</legend>
    <div class="form-group">
      <div class="col-sm-2">
      <a class="btn btn-success" id="bt_owonStartLearnIRCode">{{Start Learning}}</a>
      <a class="btn btn-success" id="bt_owonStopLearnIRCode">{{Stop Learning}}</a>
      </div>
    </div>
  </fieldset>
  
  <fieldset>
    <legend><i class="fas fa-key"></i> {{Read AC status}}</legend>
    <div class="form-group">
    <div class="col-sm-2">
        <select id="in_read_ac_status_request" class="form-control">
          <?php
          for ($i = 1; $i <= 5; $i++) { 
            echo '<option value="' . $i . '">' . $i . '</option>';
          }
          ?>
        </select>
      </div>
    
      <div class="col-sm-2">
      <a class="btn btn-success" id="bt_read_ac_status_request">{{Read AC status}}</a>
      </div>
    </div>
  </fieldset>
</form>


<script>
    $('#bt_owonValidateIRCode').off('click').on('click', function() {
    jeedom.zigbee.device.command({
      instance: zigbeeNodeInstance,
      ieee: zigbeeNodeIeee,
      endpoint: 1,
      cluster: 65453,
      cluster_type: "in",
      command: "write_multi_pairing_code_request",
      args: [
        0,
        parseInt($('#in_owonDeviceId').value()),
        parseInt($('#in_owonIRCode').value())
      ],
      error: function(error) {
        $('#div_nodeDeconzAlert').showAlert({
          message: error.message,
          level: 'danger'
        });
      },
      success: function(data) {
        $('#div_nodeDeconzAlert').showAlert({
          message: '{{IRCode envoyé avec success}}',
          level: 'success'
        });
      }
    })
  });
  $('#bt_owonValidateIRCodetest').off('click').on('click', function() {
    jeedom.zigbee.device.command({
      instance: zigbeeNodeInstance,
      ieee: zigbeeNodeIeee,
      endpoint: 1,
      cluster: 65453,
      cluster_type: "in",
      command: "write_ac_status_request",
      args: [
	      0,1,2,
        parseInt($('#in_owonIRCodesettest').value()),
      ],
      error: function(error) {
        $('#div_nodeDeconzAlert').showAlert({
          message: error.message,
          level: 'danger'
        });
      },
      success: function(data) {
        $('#div_nodeDeconzAlert').showAlert({
          message: '{{success}}',
          level: 'success'
        });
      }
    })
  });
  $('#bt_owonReadIRCode').off('click').on('click', function() {
    jeedom.zigbee.device.command({
      instance: zigbeeNodeInstance,
      ieee: zigbeeNodeIeee,
      endpoint: 1,
      cluster: 65453,
      cluster_type: "in",
      command: "read_multi_pairing_code_request",
      args: [
        0,
      ],
      error: function(error) {
        $('#div_nodeDeconzAlert').showAlert({
          message: error.message,
          level: 'danger'
        });
      },
      success: function(data) {
        $('#div_nodeDeconzAlert').showAlert({
          message: '{{IRCode lu avec success}}',
          level: 'success'
        });
      }
    })
  });
  $('#bt_owonStartLearnIRCode').off('click').on('click', function() {
    jeedom.zigbee.device.command({
      instance: zigbeeNodeInstance,
      ieee: zigbeeNodeIeee,
      endpoint: 1,
      cluster: 65453,
      cluster_type: "in",
      command: "one_key_pairing_request",
      args: [
        0,
        1,
        1
      ],
      error: function(error) {
        $('#div_nodeDeconzAlert').showAlert({
          message: error.message,
          level: 'danger'
        });
      },
      success: function(data) {
        $('#div_nodeDeconzAlert').showAlert({
          message: '{{Mode apprentissage IR Code commencé.}}',
          level: 'success'
        });
      }
    })
  });

  $('#bt_owonStopLearnIRCode').off('click').on('click', function() {
    jeedom.zigbee.device.command({
      instance: zigbeeNodeInstance,
      ieee: zigbeeNodeIeee,
      endpoint: 1,
      cluster: 65453,
      cluster_type: "in",
      command: "one_key_pairing_request",
      args: [
        0,
        1,
        0
      ],
      error: function(error) {
        $('#div_nodeDeconzAlert').showAlert({
          message: error.message,
          level: 'danger'
        });
      },
      success: function(data) {
        $('#div_nodeDeconzAlert').showAlert({
          message: '{{IRCode envoyé avec success}}',
          level: 'success'
        });
      }
    })
  });

  $('#bt_read_ac_status_request').off('click').on('click', function() {
    jeedom.zigbee.device.command({
      instance: zigbeeNodeInstance,
      ieee: zigbeeNodeIeee,
      endpoint: 1,
      cluster: 65453,
      cluster_type: "in",
      command: "read_ac_status_request",
      args: [
        0,
        parseInt($('#in_read_ac_status_request').value()),
      ],
      error: function(error) {
        $('#div_nodeDeconzAlert').showAlert({
          message: error.message,
          level: 'danger'
        });
      },
      success: function(data) {
        $('#div_nodeDeconzAlert').showAlert({
          message: '{{IRCode envoyé avec success}}',
          level: 'success'
        });
      }
    })
  });
</script>


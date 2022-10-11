"""Module to handle quirks of the  Owon AC201A
"""

import zigpy.profiles.zha as zha_p
from zigpy.quirks import CustomCluster, CustomDevice
import zigpy.types as t
from zigpy.zcl import foundation
from zigpy.zcl.clusters.general import (
    Basic,
    Groups,
    Identify,
    LevelControl,
    OnOff,
    Ota,
    Scenes,
    Time,
)
from zigpy.zcl.clusters.hvac import Thermostat
from zigpy.zcl.clusters.hvac import Fan
from zhaquirks.const import (
    DEVICE_TYPE,
    ENDPOINTS,
    INPUT_CLUSTERS,
    MODELS_INFO,
    OUTPUT_CLUSTERS,
    PROFILE_ID,
)

OWON_MANUFACTURER_CLUSTER_ID = 0xFFAD # decimal = 65453

class OwonManufacturerCluster(CustomCluster):
    """OwonManufacturerCluster manufacturer cluster."""

    cluster_id = OWON_MANUFACTURER_CLUSTER_ID
    name = "Owon Manufacturer specific"
    ep_attribute = "owon_manufacturer_specific"
    server_commands = {
        0x00: foundation.ZCLCommandDef(
            "read_multi_pairing_code_request",
            {"param1": t.uint8_t},
            is_manufacturer_specific=True,
            is_reply=False,
        ),
        0x01: foundation.ZCLCommandDef(
            "write_multi_pairing_code_request",
            {"param1": t.uint8_t,"param2": t.uint8_t,"param3": t.uint16_t},
            is_manufacturer_specific=True,
            is_reply=False,
        ),
        0x02: foundation.ZCLCommandDef(
            "read_ac_status_request",
            {
                "device_type": t.uint8_t,
                "device_id": t.uint8_t,
            },
            is_manufacturer_specific=True,
            is_reply=False,
        ),
        0x03: foundation.ZCLCommandDef(
            "write_ac_status_request",
            {
                "device_type": t.uint8_t,
                "device_id": t.uint8_t,
                "para_type": t.uint8_t,
                "param": t.uint16_t,
            },
            is_manufacturer_specific=True,
            is_reply=False,
        ),
        0x52: foundation.ZCLCommandDef(
            "one_key_pairing_request",
            {"param1": t.int8s,"param2": t.int8s,"param3": t.enum8},
            is_manufacturer_specific=True,
            is_reply=False,
        ),
    }
    client_commands = {
        0x00: foundation.ZCLCommandDef(
            "read_multi_pairing_code_response",
            {
                "device_type": t.uint8_t,
                "device_id_1": t.uint8_t,"pairing_code1": t.uint16_t,
                "device_id_2": t.uint8_t,"pairing_code2": t.uint16_t,
                "device_id_3": t.uint8_t,"pairing_code3": t.uint16_t,
                "device_id_4": t.uint8_t,"pairing_codd4": t.uint16_t,
                "device_id_5": t.uint8_t,"pairing_code5": t.uint16_t,
            },
            is_manufacturer_specific=True,
            is_reply=False,
        ),
        0x02: foundation.ZCLCommandDef(
            "read_ac_status_response",
            {
                "status": t.uint8_t,
                "device_type": t.uint8_t,
                "device_id": t.uint8_t,
                "pairing_code": t.uint16_t,
                "current_temperature": t.uint16_t,
                "system_mode": t.uint8_t,
                "heat_temperature": t.uint16_t,
                "cool_temperature": t.uint16_t,
                "fan_mode": t.uint8_t,
            },
            is_manufacturer_specific=True,
            is_reply=False,
        ),
        0x52: foundation.ZCLCommandDef(
            "one_key_pairing_response",
            #{"param1": t.int8s,"param2": t.int8s,"param3": t.enum8},
            {"param1": t.uint8_t}, #,"param2": t.uint8_t,"param3": t.enum8},
            is_manufacturer_specific=True,
            is_reply=False,
        ),
        0x80: foundation.ZCLCommandDef(
            "one_key_pairing_result_update",
            {"pairing_code_count": t.uint16_t, "pairing_code_1": t.int16s},
            is_manufacturer_specific=True,
            is_reply=False,
        ),
    }

class AC201A(CustomDevice):
    """OWON custom device."""

    signature = {
        # <SimpleDescriptor endpoint=1 profile=260 device_type=259
        # device_version=0 input_clusters=[0, 2, 3, 4, 5, 6, 1794, 2821, 65281]
        # output_clusters=[3, 4, 25]>
        MODELS_INFO: [("OWON", "AC201A")],
        ENDPOINTS: {
            1: {
                PROFILE_ID: zha_p.PROFILE_ID,
                DEVICE_TYPE: 49668,
                INPUT_CLUSTERS: [
                    Basic.cluster_id,
                    Identify.cluster_id,
                    Groups.cluster_id,
                    Scenes.cluster_id,
                    Thermostat.cluster_id,
                    Fan.cluster_id,
                    OWON_MANUFACTURER_CLUSTER_ID,
                    
                ],
                OUTPUT_CLUSTERS: [
                    Ota.cluster_id,
                ],
            }
        },
    }

    replacement = {
        ENDPOINTS: {
            1: {
                INPUT_CLUSTERS: [
                    Basic,
                    Identify,
                    Groups,
                    Scenes,
                    Thermostat,
                    Fan,
                    OwonManufacturerCluster,
                    
                ],
                OUTPUT_CLUSTERS: [
                    Ota.cluster_id,
                    OwonManufacturerCluster,
                ],
            }
        }
    }


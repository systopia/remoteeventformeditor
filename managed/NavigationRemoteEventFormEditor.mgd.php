<?php

declare(strict_types=1);

use CRM_RemoteEventFormEditor_ExtensionUtil as E;

return [
  [
    'name' => 'Navigation_Remote_Event_Form_Editor',
    'entity' => 'Navigation',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'domain_id' => 'current_domain',
        'label' => E::ts('Event Form Editor (CiviRemote)'),
        'name' => 'remote_event_form_editor',
        'url' => 'civicrm/a/#/remote-event/form-editor',
        'icon' => NULL,
        'permission' => [
          'access CiviCRM',
          'access CiviEvent',
          'edit all events',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'Events',
        'is_active' => TRUE,
        'has_separator' => 2,
        'weight' => 100,
      ],
    ],
  ],
];

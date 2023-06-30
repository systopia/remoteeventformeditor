<?php

return [
  'js' => [
    'ang/crmRemoteEventFormEditor.js',
    'ang/edit.controller.js',
    'ang/list.controller.js',
    'ang/crmRemoteEventFormEditor/*.js',
    'ang/crmRemoteEventFormEditor/*/*.js',
  ],
  'css' => [
    'ang/crmRemoteEventFormEditor.css',
  ],
  'partials' => [
    'ang/crmRemoteEventFormEditor',
  ],
  'requires' => [
    'crmUi',
    'crmUtil',
    'ngRoute',
    'ui.sortable',
    'tg.dynamicDirective',
  ],
  'settings' => [],
  'bundles' => ['bootstrap3'],
];

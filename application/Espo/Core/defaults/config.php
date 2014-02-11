<?php

return array (
  'configPath' => 'application/config.php',

  'customDir' => 'application/Espo/Custom',
  'cachePath' => 'data/cache',
  'defaultsPath' => 'application/Espo/Core/defaults',
  'unsetFileName' => 'unset.json',

  'espoModulePath' => 'Espo/Modules/{*}',
  'espoCustomPath' => 'Espo/Custom',

  'metadataConfig' =>
  array (
    'name' => 'metadata',
    'cachePath' => 'data/cache/application',
    'corePath' => 'application/Espo/Resources/metadata',
    'customPath' => 'application/Espo/Modules/{*}/Resources/metadata',
  ),

  'languageConfig' =>
  array (
    'name' => '{lang}',
    'cachePath' => 'data/cache/application/Language',
    'corePath' => 'application/Espo/Language',
    'customPath' => 'application/Espo/Modules/{*}/Language',
  ),

  'defaultPermissions' =>
  array (
    'dir' => '0775',
    'file' => '0664',
    'user' => '',
    'group' => '',
  ),
  'dateFormat' => 'MM/DD/YYYY',
  'timeFormat' => 'HH:mm',

  'cron' => array(
    'maxJobNumber' => 15, /*Max number of jobs per one execution*/    
    'jobPeriod' => 7800, /*Period for jobs, ex. if cron executed at 15:35, it will execute all pending jobs for times from 14:05 to 15:35*/
    'minExecutionTime' => 50, /*to avoid too frequency execution*/
  ),

  'systemUser' => array(
    'id' => 'system',
    'userName' => 'system',
    'firstName' => '',
    'lastName' => 'System',    
  ),

  'crud' => array(
  	'get' => 'read',
  	'post' => 'create',
  	'put' => 'update',
  	'patch' => 'patch',
  	'delete' => 'delete',
  ),
  'systemItems' =>
  array (
    'systemItems',
    'adminItems',
    'configPath',
    'cachePath',
    'metadataConfig',
    'languageConfig',
    'database',
    'customPath',
    'defaultsPath',
    'unsetFileName',
    'configPathFull',
    'configCustomPathFull',
    'crud',
    'customDir',
    'espoModulePath',
    'espoCustomPath',
    'scopeModuleMap',
  ),
  'adminItems' =>
  array (
    'defaultPermissions',
    'logger',
    'devMode',
  ),
  'currency' =>
  array(
    'base' => 'USD',
    'rate' => array(
      'EUR' => 1.37,
      'GBP' => 1.67,
    ),    
  ),
);

?>
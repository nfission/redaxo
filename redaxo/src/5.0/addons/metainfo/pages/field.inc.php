<?php

/**
 * MetaForm Addon
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

//------------------------------> Parameter

if(empty($prefix))
{
  trigger_error('Fehler: Prefix nicht definiert!', E_USER_ERROR);
  exit();
}

if(empty($metaTable))
{
  trigger_error('Fehler: metaTable nicht definiert!', E_USER_ERROR);
  exit();
}

$Basedir = dirname(__FILE__);
$field_id = rex_request('field_id', 'int');

//------------------------------> Feld loeschen
if ($func == 'delete')
{
  $field_id = rex_request('field_id', 'int', 0);
  if ($field_id != 0)
  {
    if (a62_delete_field($field_id))
      echo rex_info($REX['I18N']->msg('minfo_field_successfull_deleted'));
    else
      echo rex_warning($REX['I18N']->msg('minfo_field_error_deleted'));
  }
  $func = '';
}

//------------------------------> Eintragsliste
if ($func == '')
{
  // replace LIKE wildcards
  $likePrefix = str_replace(array('_', '%'), array('\_', '\%'), $prefix);
  
  $list = rex_list::factory('SELECT field_id, name FROM '. $REX['TABLE_PREFIX'] .'62_params WHERE `name` LIKE "'. $likePrefix .'%" ORDER BY prior');

  $list->setCaption($REX['I18N']->msg('minfo_field_list_caption'));
  $imgHeader = '<a class="rex-i-element rex-i-metainfo-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">'.$REX['I18N']->msg('add').'</span></a>';
  $list->addColumn($imgHeader, '<span class="rex-i-element rex-i-metainfo"><span class="rex-i-element-text">'.$REX['I18N']->msg('edit').'</span></span>', 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($imgHeader, array('func' => 'edit', 'field_id' => '###field_id###'));

  $list->removeColumn('field_id');
  $list->addTableColumnGroup(array(40, '*', 80));

  $list->setColumnLabel('field_id', $REX['I18N']->msg('minfo_field_label_id'));
  $list->setColumnLayout('field_id',  array('<th class="rex-small">###VALUE###</th>','<td class="rex-small">###VALUE###</td>'));

  $list->setColumnLabel('name', $REX['I18N']->msg('minfo_field_label_name'));
  $list->setColumnParams('name', array('func' => 'edit', 'field_id' => '###field_id###'));
  
  $list->addColumn('delete',$REX['I18N']->msg('delete'),-1,array('<th>'.$REX['I18N']->msg('minfo_field_label_function').'</th>','<td>###VALUE###</td>'));
  $list->setColumnParams('delete', array('func' => 'delete', 'field_id' => '###field_id###'));
  $list->addLinkAttribute('delete','onclick',"return confirm('".$REX['I18N']->msg('delete')." ?');");

  $list->setNoRowsMessage($REX['I18N']->msg('minfo_metainfos_not_found'));

  $list->show();
}
//------------------------------> Formular
elseif ($func == 'edit' || $func == 'add')
{
  $form = new rex_a62_tableExpander($prefix, $metaTable, $REX['TABLE_PREFIX'] .'62_params', $REX['I18N']->msg('minfo_field_fieldset'),'field_id='. $field_id);

  if($func == 'edit')
    $form->addParam('field_id', $field_id);

  $form->show();
}
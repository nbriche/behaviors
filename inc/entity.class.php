<?php
/**
 * @version $Id: config.class.php 68 2011-10-10 13:31:26Z remi $
 -------------------------------------------------------------------------

 LICENSE

 This file is part of Behaviors plugin for GLPI.

 Behaviors is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Behaviors is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with Behaviors. If not, see <http://www.gnu.org/licenses/>.

 @package   behaviors
 @author    David Durieux
 @copyright Copyright (c) 2010-2012 Behaviors plugin team
 @license   AGPL License 3.0 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://forge.indepnet.net/projects/behaviors
 @link      http://www.glpi-project.org/
 @since     2012

 --------------------------------------------------------------------------
*/

class PluginBehaviorsEntity extends CommonDBTM {

   static private $_instance = NULL;

   function canCreate() {
      return haveRight('config', 'w');
   }

   function canView() {
      return haveRight('config', 'r');
   }

   static function getTypeName() {
      global $LANG;

      return $LANG['common'][12];
   }

   function getName($with_comment=0) {
      global $LANG;

      return $LANG['plugin_behaviors'][0];
   }



   static function install() {
      global $DB, $LANG;

      $table = 'glpi_plugin_behaviors_entities';
      if (!TableExists($table)) { //not installed

         $query = "CREATE TABLE `$table` (
                     `id` int(11) NOT NULL AUTO_INCREMENT,
                     `entities_id` int(11) NOT NULL DEFAULT '0',
                     `use_requester_item_group` tinyint(1) default NULL,
                     `use_requester_user_group` tinyint(1) default NULL,
                     `is_ticketsolutiontype_mandatory` tinyint(1) default NULL,
                     `is_ticketrealtime_mandatory` tinyint(1) default NULL,
                     `is_requester_mandatory` tinyint(1) default NULL,
                     `is_ticketdate_locked` tinyint(1) default NULL,
                     `use_assign_user_group` tinyint(1) default NULL,
                     `sql_user_group_filter` varchar(255) default NULL,
                     `sql_tech_group_filter` varchar(255) default NULL,
                     `remove_from_ocs` tinyint(1) default NULL,
                     `date_mod` datetime default NULL,
                     PRIMARY KEY  (`id`)
                   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->query($query) or die($LANG['update'][90] . "&nbsp;:<br>" . $DB->error());
      } else {
         // Upgrade

      }
      return true;
   }
   
   
   
   /**
    * Function to uninstall / delete this table
    * 
    * @global object $DB
    * @return boolean 
    */
   static function uninstall() {
      global $DB;

      if (TableExists('glpi_plugin_behaviors_entities')) { //not installed

         $query = "DROP TABLE `glpi_plugin_behaviors_entities`";
         $DB->query($query) or die($DB->error());
      }
      return true;
   }
   
   

   static function showConfigForm($item, $withtemplate) {
      global $DB,$LANG;

      $pbEntity = new self();
      
      $hiddeninput = '';
      $query = "SELECT * FROM `glpi_plugin_behaviors_entities`
         WHERE `entities_id`='".$item->getID()."'
         LIMIT 1";
      $result = $DB->query($query);
      if ($DB->numrows($result) > 0) {
         $data = $DB->fetch_assoc($result);
         $pbEntity->getFromDB($data['id']);
      } else {
         $pbEntity->getEmpty();
         $hiddeninput = "<input type='hidden' name='entities_id' value='".$item->getID()."' />";
         $pbEntity->fields['sql_user_group_filter'] = NULL;
         $pbEntity->fields['sql_tech_group_filter'] = NULL;
      }

      $pbEntity->showFormHeader();
      
      echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2' class='tab_bg_2 b center'>".$LANG['common'][34];
      echo $hiddeninput;
      echo "</td>";   // User
      echo "<td colspan='2' class='tab_bg_2 b center'>".$LANG['Menu'][38]."</td>";     // Inventory
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][3]."&nbsp;:</td><td>";
      $value = $pbEntity->getField('sql_user_group_filter');
      if (is_null($pbEntity->fields['sql_user_group_filter'])){
         $value = 'NULL';
      }
      echo "<input type='text' name='sql_user_group_filter' value='".
           htmlentities($value,ENT_QUOTES, 'UTF-8')."' size='25'>";
      if ($value == 'NULL') {
         echo "<br/><font class='green center'>".$LANG['common'][102]."&nbsp;:&nbsp";
         echo htmlentities($pbEntity->getValueAncestor('sql_user_group_filter', $item->getID()),ENT_QUOTES, 'UTF-8');
         echo "</font>";
      }
      echo "</td><td>".$LANG['plugin_behaviors'][11]."&nbsp;:</td><td>";
      $plugin = new Plugin();
      if ($plugin->isActivated('uninstall')) {
         $pbEntity->showYesNo('remove_from_ocs', $item->getID());
      } else {
         echo $LANG['plugin_behaviors'][12];
      }
      echo "</td></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][4]."&nbsp;:</td><td>";
      $value = $pbEntity->getField('sql_tech_group_filter');
      if (is_null($pbEntity->fields['sql_tech_group_filter'])) {
         $value = 'NULL';
      }
      echo "<input type='text' name='sql_tech_group_filter' value='".
           htmlentities($value,ENT_QUOTES, 'UTF-8')."' size='25'>";
      if ($value == 'NULL') {
         echo "<br/><font class='green center'>".$LANG['common'][102]."&nbsp;:&nbsp";
         echo htmlentities($pbEntity->getValueAncestor('sql_tech_group_filter', $item->getID()),ENT_QUOTES, 'UTF-8');
         echo "</font>";
      }
      echo "</td><td colspan='2' class='tab_bg_2 b center'>".$LANG['setup'][704];     // Notifications
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2' class='tab_bg_2 b center'>".$LANG['job'][13]."</td>";      // New ticket
      echo "<td colspan='2'>";
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][1]."&nbsp;:</td><td>";
      $pbEntity->showYesNo('use_requester_item_group', $item->getID());
      echo "</td><td rowspan='8' colspan='2' class='top'>";
      echo "<br>".$LANG['common'][26]."&nbsp;: ";
      echo convDateTime($pbEntity->fields["date_mod"]);
      echo "</td></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][2]."&nbsp;:</td><td>";
      $pbEntity->showYesNo('use_requester_user_group', $item->getID());
      echo "</td></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][6]."&nbsp;:</td><td>";
      $pbEntity->showYesNo('use_assign_user_group', $item->getID());
      echo "</td></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][13]."&nbsp;:</td><td>";
      $pbEntity->showYesNo('is_requester_mandatory', $item->getID());
      echo "</td></tr>";

      echo "<tr class='tab_bg_1'>"; // Ticket - Update
      echo "<td colspan='2' class='tab_bg_2 b center'>".$LANG['job'][38].' - '.$LANG['buttons'][14];
      echo "</td></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][7]."&nbsp;:</td><td>";
      $pbEntity->showYesNo('is_ticketrealtime_mandatory', $item->getID());
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][8]."&nbsp;:</td><td>";
      $pbEntity->showYesNo('is_ticketsolutiontype_mandatory', $item->getID());
      echo "</td></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_behaviors'][14]."&nbsp;:</td><td>";
      $pbEntity->showYesNo('is_ticketdate_locked', $item->getID());
      echo "</td></tr>";

      $pbEntity->showFormButtons(array('candel'=>false));

      return false;
   }

   
   
   /**
    * Dropdown yes/no and get inheritance
    * 
    * @global array $LANG
    * @param value $name field name
    * @param interget $entities_id 
    */
   function showYesNo($name, $entities_id) {
      global $LANG;
      
      $elements = array("NULL" => $LANG['common'][102],
                        "+0" => $LANG['choice'][0],
                        "+1" => $LANG['choice'][1]
                        );
      $value = (is_null($this->fields[$name]) ? "NULL" : "+".$this->fields[$name]);

      Dropdown::showFromArray($name, $elements, array('value' => $value));
      if (is_null($this->fields[$name])) {
         echo "<br/><font class='green center'>".$LANG['common'][102]."&nbsp;:&nbsp";
         echo Dropdown::getYesNo($this->getValueAncestor($name, $entities_id));
         echo "</font>";
      }
   }
   
   
   
   /**
    * Get value of config
    * 
    * @global object $DB
    * @param value $name field name 
    * @param integer $entities_id
    * 
    * @return value of field 
    */
   function getValueAncestor($name, $entities_id) {
      global $DB;      

      $entities_ancestors = getAncestorsOf("glpi_entities", $entities_id);

      $nbentities = count($entities_ancestors);
      for ($i=0; $i<$nbentities; $i++) {
         $entity = array_pop($entities_ancestors);
         $query = "SELECT * FROM `".$this->getTable()."`
            WHERE `entities_id`='".$entity."'
               AND `".$name."` IS NOT NULL
            LIMIT 1";
         $result = $DB->query($query);
         if ($DB->numrows($result) != '0') {
            $data = $DB->fetch_assoc($result);
            return $data[$name];
         }
      }
      // Not find in entities, so get value of general config
      $config = PluginBehaviorsConfig::getInstance();

      return $config->getField($name);      
   }
   
   
   
   /**
    * Get the value (of this entity or parent entity or in general config
    *
    * @global object $DB
    * @param value $name field name
    * @param integet $entities_id
    * 
    * @return value value of this field 
    */
   function getValue($name, $entities_id) {
      global $DB;
      
      $query = "SELECT * FROM `glpi_plugin_behaviors_entities`
         WHERE `entities_id`='".$entities_id."'
         LIMIT 1";
      $result = $DB->query($query);
      if ($DB->numrows($result) > 0) {
         $data = $DB->fetch_assoc($result);
         return $data[$name];
      } else {
         return $this->getValueAncestor($name, $entities_id);
      }
   }
   
   
   
   
   function prepareInputForAdd($input) {
      global $LANG, $DB;

      if (isset($input['sql_user_group_filter']) && !empty($input['sql_user_group_filter'])) {
         $sql = "SELECT id
                 FROM `glpi_groups`
                 WHERE (".stripslashes($input['sql_user_group_filter']).")";
         $res = $DB->query($sql);
         if ($res) {
            $DB->free_result($res);
         } else {
            addMessageAfterRedirect($LANG['plugin_behaviors'][5] .
                                       " (".stripslashes($input['sql_user_group_filter']).")",
                                    false, ERROR);
            addMessageAfterRedirect($DB->error());
            unset($input['sql_user_group_filter']);
         }
      }
      if (isset($input['sql_tech_group_filter']) && !empty($input['sql_tech_group_filter'])) {
         $sql = "SELECT id
                 FROM `glpi_groups`
                 WHERE (".stripslashes($input['sql_tech_group_filter']).")";
         $res = $DB->query($sql);
         if ($res) {
            $DB->free_result($res);
         } else {
            addMessageAfterRedirect($LANG['plugin_behaviors'][5] .
                                       " (".stripslashes($input['sql_tech_group_filter']).")",
                                    false, ERROR);
            addMessageAfterRedirect($DB->error());
            unset($input['sql_tech_group_filter']);
         }
      }
      return $input;
   }
   
   function prepareInputForUpdate($input) {
      return $this->prepareInputForAdd($input);
   }

}

?>
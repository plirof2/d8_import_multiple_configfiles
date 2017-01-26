<?php

/**
 * @file
 * Contains \Drupal\config_devel\EventSubscriber\ConfigDevelAutoImportSubscriber.
 */

namespace Drupal\config_devel\EventSubscriber;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Config\InstallStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\ConfigEvents; //added by J

class ConfigDevelAutoImportSubscriber extends ConfigDevelSubscriberBase implements EventSubscriberInterface {
  /**
   * Reinstall changed config files.
   */
  public function autoImportConfig() {
    $config = $this->getSettings();
    $check_to_import_once = $config->get('check_to_import_once');
    //drupal_set_message('<pre> config->get(check_to_import_once) '. print_r($config->get('check_to_import_once')) .'</pre> $check_to_import_once='.$check_to_import_once[0] );  //debug _jon
    //if(null !== ($config->get('check_to_import_once'))) drupal_set_message('check_to_import_once is SET'); //debug jon
    if(($config->get('check_to_import_once')==1)) drupal_set_message('Confi_devel will parse all .yml files (check_to_import_once is CHECKED)'); //debug jon
    if(($config->get('check_to_import_once')==0)) {
      //drupal_set_message('check_to_import_once is UN-CHECKED'); //debug jon
      return; //just EXIT if box is unchecked
    }
    if(($config->get('check_to_import_once')==1)) {
    // $config->set('check_to_import_once',null); // This SETS the checkbox value NOT the saved one debug jon
    //$this->getSettings("config_devel.settings")->set('check_to_import_once',false);
   }
    //  drupal_set_message('<pre>aaa '. print_r($config->get('auto_import')) .'</pre>');  //debug _jon
    $changed = FALSE;
    foreach ($config->get('auto_import') as $key => $file) {  //for each FILE detected do the following :
      //drupal_set_message('<pre>aaa KEY '. print_r($key) .'</pre>');  //debug _jon
      //drupal_set_message('<pre>aaa FILE '. print_r($file) .'</pre>');  //debug _jon system. simple
      //drupal_set_message("'public function autoImportConfig > foreach (config->get('auto_import')" );
      //drupal_set_message('<pre>aaa file[filename]'. print_r($file['filename']) .'</pre>');  //debug _jon
      if ($new_hash = $this->importOne($file['filename'], $file['hash'])) {
        $config->set("auto_import.$key.hash", $new_hash); //ORIG line disabled for testing
        //drupal_set_message('<pre>Hello from if (new_hash = this->importOne'. print_r($file['filename']) .'</pre>');  //debug _jon
        $changed = TRUE;
      }
    }
    if ($changed) {
      $config->save();
    }
  }

  /**
   * Registers the methods in this class that should be listeners.
   *
   * @return array
   *   An array of event listener definitions.
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('autoImportConfig', 20); //orig line
    //$events[ConfigEvents::SAVE][] = array('autoImportConfig', 20);
    return $events;
  }

  /**
   * @param string $filename
   * @param string $original_hash
   * @return bool
   */
  public function importOne($filename, $original_hash = '', $contents = '') {
    //drupal_set_message('Hello   public function importOne 01 ');  //debug_jon
    //drupal_set_message('Hello   public function importOne 01 FILE GET CONTENTS TEST :'.file_get_contents("views.view.coursefields.yml"));  //debug_jon
    //drupal_set_message('Hello   public function importOne 01 FILE GET CONTENTS TEST config_devel/import_these:'.file_get_contents("modules/config_devel/import_these/views.view.coursefields.yml"));  //debug_jon
    $hash = '';
    $import_these_folder_location="modules/config_devel/import_these/";
    $rename_after_usage=false; // SHould I rename the config .yml I used AFTER I import it - since this module runs in EVERY refresh
    $clear_filelist_after_usage=true; // SHould I rename the config .yml I used AFTER I import it - since this module runs in EVERY refresh
    //drupal_set_message('importOne 02 BEFORE $contents='.$contents);  //debug_jon
    if (!$contents && (!$contents = @file_get_contents($import_these_folder_location.$filename))) {
        drupal_set_message("importOne no content found (maybe file does not exist OR is in wrong PATH)");  //debug_jon
      return $hash; //jon commented for debug
    }
    //drupal_set_message('importOne 02 AFTER $contents='.$contents);  //debug_jon
    $needs_import = TRUE;
    if ($original_hash) {
      drupal_set_message('importOne 02 if original_hash ');  //debug_jon
      $hash = Crypt::hashBase64($contents);
      if ($hash == $original_hash) {
        $needs_import = FALSE;
      }
    }
    if ($needs_import) {
      drupal_set_message('importOne 02 - needs_import - Starting Import Process -'.date('Y-m-d_Hi'));  //debug_jon
      $data = (new InstallStorage())->decode($contents);
      $config_name = basename($filename, '.yml');
      //drupal_set_message('Hello   public function importOne 02 if needs_import $config_name='.$config_name);  //debug_jon
      $entity_type_id = $this->configManager->getEntityTypeIdByName($config_name);
      if ($entity_type_id) {
        $entity_storage = $this->getStorage($entity_type_id);
        $entity_id = $this->getEntityId($entity_storage, $config_name);
        $entity_type = $entity_storage->getEntityType();
        $id_key = $entity_type->getKey('id');
        $data[$id_key] = $entity_id;
        /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
        $entity = $entity_storage->create($data);
        if ($existing_entity = $entity_storage->load($entity_id)) {
          $entity
            ->set('uuid', $existing_entity->uuid())
            ->enforceIsNew(FALSE);
        }
        $entity_storage->save($entity);
      }
      else {
        $this->configFactory->getEditable($config_name)->setData($data)->save();
      }
      if ($rename_after_usage) { $this->renameFile($filename,$import_these_folder_location);}
    }
    return $hash;
  }

  /**
   * @param string $filename
   * @param string $import_these_folder_location
   * @return bool
   */
  public function renameFile($filename,$import_these_folder_location='') {
    $datesuffix=".IMPORTED-".date('Y-m-d_Hi');
    drupal_set_message('renameFile oldfile='.$filename.' new filename will be ='.$filename.$datesuffix . " PATH= ".drupal_get_path('module', "config_devel"));  //debug_jon
    //rename($import_these_folder_location.$filename, $import_these_folder_location.$filename.$datesuffix);
  //  $file = \Drupal\file\Entity\File::load($fid);
  //  file_move(FileInterface $source, 'path/to/destination');
  }
}

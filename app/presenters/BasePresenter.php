<?php
namespace App\Presenters;

use Nette\Application\UI;

class BasePresenter extends UI\Presenter {

  /**
  * Formats layout template file names.
  * @return array
  */
  public function formatLayoutTemplateFiles() {    
    $layoutFiles = parent::formatLayoutTemplateFiles();    
    $layoutFiles[] = __DIR__ . "/../templates/@layout.latte";
    return $layoutFiles;
  }

}
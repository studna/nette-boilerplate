<?php 

class ExtendedZipArchive extends ZipArchive { 

  private $directories = Array();

  protected $outputFilename;

  public function open($filename, $flags = null) {
    $this->outputFilename = $filename;
    return parent::open($filename, $flags);
  }

  public function addDir($location, $name) { 
    if(isset($this->directories[$name]))
      return;    

    echo "Moving directory: $name\n\r";

    $this->directories[$name] = true;
    $this->addDirFiles($location, $name); 
  }

  public function cleanDir($name) {
    echo "Cleaning directory: $name \n\r";
    $this->deleteName($name);
    $this->addEmptyDir($name);
  }

  public function close() {    

    echo "\nProduction file \"" . basename($this->outputFilename) ."\" was generated.\n"; 
    return parent::close();
  }

  private function addDirFiles($location, $name) {       
      $location = realpath($location);
      $location = str_replace('\\', '/', $location);
      $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($location), RecursiveIteratorIterator::SELF_FIRST);
      
      foreach($files as $file) {
        
        $filePath = realpath($file);
        $filePath = str_replace('\\', '/', $filePath);

        $parts = explode($location, $filePath);
                
        if(!isset($parts[1]) || empty($parts[1]))
         continue;

       $relativePath = $name . $parts[1];

        if(is_dir($filePath)) {                             
          $this->addEmptyDir($relativePath);
          $this->addDir($filePath, $relativePath);          
        } else {
          $filename = $name . "/" . basename($filePath);  
          
          if(file_exists(__DIR__ . "/" . $filename))
            $this->addFromString($filename, file_get_contents($filePath));
        }
      }
      
  }
} 

$filename = __DIR__ . "/production.zip";

if(file_exists($filename))
  unlink($filename);

$archive = new ExtendedZipArchive;
$archive->open($filename, ZipArchive::CREATE);
$archive->addDir(__DIR__ . "/app", "app");
$archive->addEmptyDir("log");
$archive->addDir(__DIR__ . "/temp", "temp");
$archive->cleanDir("temp/cache");
$archive->addDir(__DIR__ . "/vendor", "vendor");
$archive->addDir(__DIR__ . "/public", "public");
$archive->close();    
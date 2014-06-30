<?php 

class ExtendedZipArchive extends ZipArchive { 

  private $directories = Array();

  protected $outputFilename;

  protected $movedFoldersCount = 0;

  protected $movedFilesCount = 0;

  protected $moved = Array();

  private function move($filename, $name) {
    if(!file_exists($filename))
      return;

    if(isset($this->moved[$filename]))
      return;

    $this->movedFilesCount++;
    $this->moved[$filename] = true;  
    $this->addFromString($name, file_get_contents($filename));
  }

  public function open($filename, $flags = null) {
    $this->outputFilename = $filename;
    return parent::open($filename, $flags);
  }

  public function addDir($location, $name) { 
    if(isset($this->directories[$name]))
      return;

    echo "Moving directory: $name\n\r";

    $this->movedFoldersCount++;
    $this->directories[$name] = true;

    return $this->addDirFiles($location, $name); 
  }

  public function cleanDir($name) {
    echo "Cleaning directory: $name \n\r";
    $this->deleteName($name);
    $this->addEmptyDir($name);
  }

  public function close() {    
    echo "\nMoved {$this->movedFilesCount} files and {$this->movedFoldersCount} folders.\n";
    echo "\nProduction file \"" . basename($this->outputFilename) ."\" was generated.\n"; 
    return parent::close();
  }

  private function addDirFiles($location, $name) {       
      $location = realpath($location);
      $location = str_replace('\\', '/', $location);
      
      $recursiveDirectoryIterator = new RecursiveDirectoryIterator($location, 
        RecursiveDirectoryIterator::SKIP_DOTS);

      $files = new RecursiveIteratorIterator($recursiveDirectoryIterator, 
        RecursiveIteratorIterator::SELF_FIRST);
      
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
          $filename = $relativePath;  
          $this->move($filePath, $filename);          
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
$archive->addFile(".htaccess", ".htaccess");
$archive->addFile(".webconfig", ".webconfig");
$archive->close();    
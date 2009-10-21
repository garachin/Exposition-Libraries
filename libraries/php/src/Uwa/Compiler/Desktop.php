<?php
/**
 * Copyright Netvibes 2006-2009.
 * This file is part of Exposition PHP Lib.
 *
 * Exposition PHP Lib is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Exposition PHP Lib is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Exposition PHP Lib.  If not, see <http://www.gnu.org/licenses/>.
 */


require_once 'Compiler.php';
require_once 'Zend/Http/Client.php';

/**
 * Desktop Compiler.
 */
abstract class Uwa_Compiler_Desktop extends Compiler
{
    /**
     * Archive Format of the widget.
     *
     * @var string
     */
    protected $archiveFormat = 'zip';

    /**
     * Instance of Archive Class.
     *
     * @var string
     */
    protected $archiveClass;

    /**
     * Constructor.
     *
     * @param string  $url
     */
    public function __construct($parser)
    {
        parent::__construct($parser);

        // Temporary file path to build the archive
        $tmpFile = Zend_Registry::get('tmpDir') . 'compile' . time() . rand(1, 1000) . '.zip';

        $this->archiveclass Uwa_= Compiler_Desktop_Archive_Factory::newArchive($this->archiveFormat, $tmpFile);
    }

    /**
     * Retrieves the content of the ZIP archive.
     *
     * @return string
     */
    public function getFileContent()
    {
        // Archive filling according to the selected compiler
        $this->buildArchive();

        // Archive closing
        $this->archiveClass->createArchive();

        return $this->archiveClass->getArchiveContent();
    }

    /**
     * Renders the compiled widget with the correct mime type and file name.
     */
    public function render()
    {
        header('Pragma: public');
        header('Content-type: ' . $this->getFileMimeType());
        header('Content-Disposition: attachment; filename="' . $this->getFileName() . '"');
        echo $this->getFileContent();
    }

    /*** ZIP ARCHIVE DEPRECATED FUNCTIONS ALIAS ***/

    protected function addDirToZip($directory, $root = '')
    {
        return $this->addDirToArchive($directory, $root);
    }

    /**
     * Adds a file to the ZIP archive.
     *
     * @param  string  $path The path of the file to add
     * @param  string  $zipEntryName The ZIP archive name
     * @return boolean True if the file has been successfully added, otherwise false
     */
    protected function addFileToZip($path, $zipEntryName)
    {
        return $this->addFileToArchive($path, $zipEntryName);
    }

    /**
     * Adds a distant file to the ZIP archive.
     *
     * @param  string $url
     * @param  string $filePath
     * @param  string $dirPath
     * @return string The file path into the archive.
     */
    protected function addDistantFileToZip($url, $filePath = '', $dirPath = '')
    {
        return $this->addDistantFileToArchive($url, $filePath, $dirPath);
    }

    /**
     * Adds a file to the ZIP Archive.
     *
     * @param  string  $path The path of the file to add
     * @param  string  $EntryName The ZIP archive name
     * @return boolean True if the file has been successfully added, otherwise false
     */
    protected function addFileFromStringToZip($EntryName, $contents)
    {
        return $this->addFileFromStringToArchive($EntryName, $contents);
    }

    /*** ARCHIVE FUNCTIONS ***/

    /**
     * Adds a file to the Archive.
     *
     * @param  string  $path The path of the file to add
     * @param  string  $EntryName The ZIP archive name
     * @return boolean True if the file has been successfully added, otherwise false
     */
    protected function addFileFromStringToArchive($EntryName, $contents)
    {
        return $this->archiveClass->addFileFromString($EntryName, $contents);
    }

    /**
     * Adds a directory to the ZIP archive.
     *
     * @param string $directory The directory name to add
     * @param string $root The root destination within the archive
     */
    protected function addDirToArchive($directory, $root = '')
    {
        return $this->archiveClass->addFiles($directory, $root);
    }

    /**
     * Adds a file to the Archive.
     *
     * @param  string  $path The path of the file to add
     * @param  string  $EntryName The archive name
     * @return boolean True if the file has been successfully added, otherwise false
     */
    protected function addFileToArchive($path, $EntryName)
    {
        return $this->archiveClass->addFile($path, $EntryName);
    }

    /**
     * Adds a distant file to the Archive.
     *
     * @param  string $url
     * @param  string $filePath
     * @param  string $dirPath
     * @return string The file path into the archive.
     */
    protected function addDistantFileToArchive($url, $filePath = '', $dirPath = '')
    {
        $fileInfo = parse_url($url);
        if(!isset($fileInfo['host'])) {
            $urlInfo = parse_url($this->_url);
            $url = $urlInfo['scheme'] . '://' . $urlInfo['host'] . dirname( $urlInfo['path'] ) . '/' . $url;
        }
        if (empty($filePath) && empty($dirPath)) {
            $filePath = 'widget/' . basename($fileInfo['path']);
        } else if (!empty($dirPath)) {
            $filePath = $dirPath . basename($fileInfo['path']);
        }
        if ($this->addFileToArchive($url, $filePath)) {
            return $filePath;
        } else {
            return '';
        }
    }

    /*** ABSTRACT FUNCTIONS ***/

    abstract protected function buildArchive();

    abstract public function getFileName();

    abstract public function getFileMimeType();
}
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


require_once 'Compiler/Desktop.php';

/**
 * Jil Widgets Compiler.
 */
final class Compiler_Desktop_Vista extends Compiler_Desktop
{
    /**
     * Archive Format of the widget.
     *
     * @var string
     */
    protected $archiveFormat = 'Zip';

    /**
     * Width of the widget.
     *
     * @var string
     */
    protected $_width = 330;

    /**
     * Height of the widget.
     *
     * @var string
     */
    protected $_height = 370;

    /**
     * Extension.
     *
     * @var string
     */
    protected $_extension = 'gadget';

    /**
     * Compiler Name.
     *
     * @var string
     */
    protected $_platform = 'vista';

    /**
     * Mime Type.
     *
     * @var string
     */
    protected $_mimeType = 'application/x-binary';

    protected function buildArchive()
    {
        // Add the widget skeleton to the archive
        $ressourcesDir = Zend_Registry::get('uwaRessourcesDir');
        if (!is_readable($ressourcesDir)) {
            throw new Exception('UWA ressources directory is not readable.');
        }

        $this->addDirToArchive($ressourcesDir . 'vista');

        $this->addFileFromStringToZip('UWA.html', $this->getHtml(false));

        $this->addFileFromStringToZip('flyout.html', $this->getHtml(true));

        $this->addFileFromStringToZip('gadget.xml', $this->_getXmlManifest());
    }

    protected function getHtml($vistaModule = false)
    {
        $icon = $this->_widget->getIcon();
        if (empty($icon)) {
            $icon = 'http://' . NV_STATIC . '/modules/uwa/icon.png';
        }

        $l = array();

        $l[] = '<html>';
        $l[] = '<head>';
        $l[] = '    <title>' . $this->_widget->getTitle() . '</title>';
        $l[] = '    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
        $l[] = '    <link rel="icon" href="' . $icon . '" type="image/x-icon" />';

        /* @todo reverve ingenering */
        $l[] = '    <link rel="stylesheet" type="text/css" href="http://www.netvibes.com/themes/uwa/vista/vista.css" />';
        $l[] = '    <script type="text/javascript" src="http://www.netvibes.com/js/UWA/load.js.php?env=Vista"></script>';
        $l[] = '    <script type="text/javascript" src="http://www.netvibes.com/api/uwa/compile/uwa_javascript.php?platform=vista&className=CompiledModule&moduleUrl=' . urlencode($this->_widget->getUrl()) . '"></script>';


        $l[] = '    <script type="text/javascript" src="js/VistaModule.js"></script>';
        $l[] = '    <script type="text/javascript" src="js/PrefsForm.js"></script>';
        $l[] = '    <script type="text/javascript">';
        $l[] = '        var vistaModule = new VistaModule( ' . ($vistaModule ? 'true' : 'false')  . ' );';
        $l[] = '    </script>';

        $l[] = '</head>';
        $l[] = '<body onload="vistaModule.load()">';
        $l[] = '<div id="vistaContent">';

        $l[] = $this->_getHtmlBody();

        if (isset($this->options['appendBody'])) {
            $l[] = $this->options['appendBody'];
        }

        $l[] = '</div>';
        $l[] = '<g:background id="vBackground" src="img/transparent.png" style="position:absolute;left:0;width:329px;height:550px;top:0;z-index:-2" />';
        $l[] = '</body>';
        $l[] = '</html>';

        return implode("\n", $l);
    }

    protected function _getHtmlBody()
    {
        $title = $this->_widget->getTitle();

        $l = array();

        $l[] = '<div id="wrapper">';
        $l[] = '    <div id="moduleHeader" class="moduleHeader">';
        $l[] = '		<div class="refresh"><img src="img/refresh.png" onclick="vistaModule.refresh()"></div>';
        $l[] = '        <div id="moduleTitle" class="title">' . $this->_widget->getTitle() . '</div>';
        $l[] = '    </div>';

        $l[] = '    <div id="contentWrapper">';
        $l[] = '        <div class="moduleContent" id="moduleContent">';
        $l[] = '            <p>Loading...</p>';
        $l[] = '        </div>';
        $l[] = '    </div>';

        $l[] = '    <div class="moduleFooter" id="moduleFooter">';
        $l[] = '        &nbsp;';
        $l[] = '    </div>';
        $l[] = '</div>';

        return implode("\n", $l);
    }

    protected function _getXmlManifest()
    {
        $title = $this->_widget->getTitle();
        $metas = $this->_widget->getMetas();

        $l = array();
        $l[] = '<?xml version="1.0" encoding="utf-8"?>';
        $l[] = '<gadget>';
        $l[] = '    <name>' . htmlspecialchars($title) . '</name>';
        $l[] = '    <namespace>UWA</namespace>';
        $l[] = '    <version>1.0.0.0</version>';
        $l[] = '    <hosts>';
        $l[] = '        <host name="sidebar">';
        $l[] = '            <base type="HTML" apiVersion="1.0.0" src="UWA.html"/>';
        $l[] = '            <permissions>Full</permissions>';
        $l[] = '            <platform minPlatformVersion="1.0"/>';
        $l[] = '        </host>';
        $l[] = '    </hosts>';
        $l[] = '<author name="' . htmlspecialchars($metas['author']) . '"/>';
        $l[] = '<description>' . htmlspecialchars($metas['description']) . '</description>';
        $l[] = '<icons>';
        $l[] = '    <icon src="img/UWA.png"/>';
        $l[] = '</icons>';
        $l[] = '</gadget>';

        return implode("\n", $l);
    }

    public function getFileName()
    {
        return $this->getNormalizedTitle() . '.' . $this->_extension;
    }

    public function getNormalizedTitle()
    {
        $filename = preg_replace('/[^a-z0-9]/i', '', $this->_widget->getTitle());
        if (!empty($filename)) {
            return $filename;
        } else {
            return 'Widget';
        }
    }

	public function getFileMimeType()
    {
        return $this->_mimeType;
    }
}
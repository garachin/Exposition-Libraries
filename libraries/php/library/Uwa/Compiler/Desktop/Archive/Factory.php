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

/**
 * Compiler Desktop Archive Factory.
 */
class Uwa_Compiler_Desktop_Archive_Factory
{
    static public function newArchive($format, $fileName)
    {
        $className = 'Compiler_Desktop_Archive_' . ucfirst(strtolower($format));

        // prevent stack error
        Zend_Loader::loadClass($className);

        return new $className($fileName);
    }
}

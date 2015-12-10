<?php
/**
 * 2015 Darwin Pricing
 *
 * For support please visit www.darwinpricing.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU Lesser General Public License (LGPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/lgpl.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@darwinpricing.com so we can send you a copy immediately.
 *
 *  @author    Darwin Pricing <support@darwinpricing.com>
 *  @copyright 2015 Darwin Pricing
 *  @license   http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License (LGPL 3.0)
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'darwinpricing` (
    `id_darwinpricing` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY  (`id_darwinpricing`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 10-10-2019 16:33
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Bitpatroon\Typo3Tca2\Backend\FormDataProvider;

use TYPO3\CMS\Backend\Configuration\TypoScript\ConditionMatching\ConditionMatcher;

class ConditionalDataProvider implements \TYPO3\CMS\Core\SingletonInterface, \TYPO3\CMS\Backend\Form\FormDataProviderInterface
{

    /** @var bool[] */
    private $innerCache = [];

    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {

        $pageTsConfig = $result['pageTsConfig']['TCEFORM.'];
        if (!empty($pageTsConfig)) {
            $result = $this->processCondition($result);
        }
        return $result;
    }

    /**
     * @param array $result
     */
    private function processCondition(array $result)
    {
        $tceForm = $result['pageTsConfig']['TCEFORM.'];
        foreach ($tceForm as $table => $tablConfig) {
            if (empty($tablConfig)) {
                continue;
            }
            foreach ($tablConfig as $columnId => $columnConfig) {
                if (empty($columnConfig)) {
                    continue;
                }
                if (!is_array($columnConfig)) {
                    continue;
                }

                foreach ($columnConfig as $propertyConditionName => $propertyConfig) {
                    if (substr($propertyConditionName, -1) !== '.') {
                        // skip all ending with a .
                        continue;
                    }

                    if (isset($columnConfig[$propertyConditionName]['if.'])) {
                        $propertyCondition = $columnConfig[$propertyConditionName]['if.'];
                        if (empty($propertyCondition)) {
                            continue;
                        }
                        $conditionIsMet = $this->conditionIsMet($propertyCondition);
                        if (!$conditionIsMet) {
                            $propertName = substr($propertyConditionName, 0, -1);
                            unset($result['pageTsConfig']['TCEFORM.'][$table][$columnId][$propertName]);
                            unset($result['pageTsConfig']['TCEFORM.'][$table][$columnId][$propertyConditionName]);
                            continue;
                        }

                        unset($result['pageTsConfig']['TCEFORM.'][$table][$columnId][$propertyConditionName]['if.']);
                    }

                    // check all subitems i.e. when in a n addItems {  ...  } construct
                    foreach ($columnConfig[$propertyConditionName] as $subitemKey => $subitemConfig) {
                        if (substr($subitemKey, -1) !== '.') {
                            // skip all ending with a .
                            continue;
                        }
                        if ($subitemKey === "if.") {
                            continue;
                        }
                        $subPropertyCondition = $columnConfig[$propertyConditionName][$subitemKey]['if.'];
                        if (empty($subPropertyCondition)) {
                            continue;
                        }
                        $conditionIsMet = $this->conditionIsMet($subPropertyCondition);
                        if (!$conditionIsMet) {
                            $subitemPropertyName = substr($subitemKey, 0, -1);
                            unset($result['pageTsConfig']['TCEFORM.'][$table][$columnId][$propertyConditionName][$subitemKey]);
                            unset($result['pageTsConfig']['TCEFORM.'][$table][$columnId][$propertyConditionName][$subitemPropertyName]);
                        } else {
                            unset($result['pageTsConfig']['TCEFORM.'][$table][$columnId][$propertyConditionName][$subitemKey]);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Checks if a condition is met
     * @param $propertyCondition
     */
    private function conditionIsMet($propertyCondition)
    {
        if (empty($propertyCondition)) {
            return false;
        }

        if (isset($propertyCondition['userFunc'])) {
            $userFunc = $propertyCondition['userFunc'];
            $condition = sprintf('[userFunc = %s]', trim($userFunc));
            if (isset($this->innerCache[$condition])) {
                return $this->innerCache[$condition];
            }

            /** @var ConditionMatcher $conditionMatcher */
            $conditionMatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ConditionMatcher::class);
            $result = $conditionMatcher->match($condition);

            $this->innerCache[$condition] = $result ? true : false;

            return $result;
        }

        return true;
    }
}

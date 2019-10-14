<?php

if (!defined('TYPO3_MODE')) {
    die ('¯\_(ツ)_/¯');
}

if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\Bitpatroon\Typo3Tca2\Backend\FormDataProvider\ConditionalDataProvider::class] = [
        'depends' => [
            \TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord::class,
            \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfigMerged::class,
        ],
        'before' => [
            \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class,
            \TYPO3\CMS\Backend\Form\FormDataProvider\TcaTypesShowitem::class
        ]
    ];
}

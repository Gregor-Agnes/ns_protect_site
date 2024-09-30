<?php
defined('TYPO3') or die();

// Add new fields to pages:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',
    [
        'tx_nsprotectsite_protection' => [
            'label' => 'LLL:EXT:ns_protect_site/Resources/Private/Language/locallang_db.xlf:enableprotect',
            'exclude' => 1,
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
            'onChange' => 'reload'
        ],
        'tx_nsprotectsite_protect_password' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:tx_nsprotectsite_protection:>:0',
            'label' => 'LLL:EXT:ns_protect_site/Resources/Private/Language/locallang_db.xlf:addpass',
            'config' => [
                'type' => 'input',
                'max' => 100,
                'eval' => 'trim,required,password,saltedPassword',
            ]
        ]
    ]);



    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'pages', // Table name
    '--div--;Seite schützen, tx_nsprotectsite_protection, --linebreak--, tx_nsprotectsite_protect_password',
        '1' // List of specific types to add the field list to. (If empty, all type entries are affected)
    );

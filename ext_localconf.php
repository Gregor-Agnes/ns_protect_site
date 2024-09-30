<?php
defined('TYPO3') || die('Access denied.');

    //$moduleClass = \Nitsan\NsProtectSite\Controller\ProtectPagesController::class;

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'NsProtectSite',
    'NsprotectsiteLoad',
    [
        \Nitsan\NsProtectSite\Controller\ProtectPagesController::class => 'load'
    ]
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'NsProtectSite',
    'NsprotectsiteLogin',
    [
        \Nitsan\NsProtectSite\Controller\ProtectPagesController::class => 'login'
    ],
    // non-cacheable actions
    [
        \Nitsan\NsProtectSite\Controller\ProtectPagesController::class => 'login'
    ]
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'NsProtectSite',
    'NsprotectsiteForm',
    [
        \Nitsan\NsProtectSite\Controller\ProtectPagesController::class => 'form'
    ]
);

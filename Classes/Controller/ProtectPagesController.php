<?php
namespace Nitsan\NsProtectSite\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

GeneralUtility::makeInstance(\TYPO3\CMS\Install\Service\SessionService::class)->startSession();

/***
 *
 * This file is part of the "[Nitsan] Protect site" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019
 *
 ***/
/**
 * ProtectPagesController
 */
class ProtectPagesController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * action list
     *
     * @return bool
     */
    public function loadAction(): ResponseInterface
    {
        $data = $GLOBALS['TSFE']->page;
        $pageUid = $data['uid'];

        if (isset($_SESSION['password-' . $pageUid . '-protect'])) {
            return true;
        } else {
            $isActive = $data['tx_nsprotectsite_protection'];
            if ($isActive) {
                $pageUid = $data['uid'];
                $uriBuilder = $this->uriBuilder;
                $uri = $uriBuilder
                    ->setTargetPageUid($pageUid)
                    ->setArguments(['type' => '88889'])
                    ->setCreateAbsoluteUri(true)
                    ->build();
                return $this->responseFactory->createResponse(307)
                    ->withHeader('Location', $uri);
            }
        }
        return true;
    }

    /**
     * action login
     *
     * @return void
     * @throws InvalidPasswordHashException|StopActionException
     */
    public function loginAction(): ResponseInterface
    {
        $params = $this->request->getParsedBody()['tx_nsprotectsite_nsprotectsiteform'];
        
        $data = $GLOBALS['TSFE']->page;
        $saltedPassword = $data['tx_nsprotectsite_protect_password'];
        $pass = $params['pass'];

        $success = false;
        if ($GLOBALS['TYPO3_CONF_VARS']['BE']['loginSecurityLevel'] == 'rsa') {
           
                $objSalt = (new \TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory)->get($saltedPassword, 'BE');
            
            if (is_object($objSalt)) {
                $success = $objSalt->checkPassword($pass, $saltedPassword);
            }
        } elseif ($GLOBALS['TYPO3_CONF_VARS']['BE']['loginSecurityLevel'] == 'md5') {
            $password = md5($pass);
            if ($saltedPassword == $password) {
                $success = true;
            }
        } elseif ($GLOBALS['TYPO3_CONF_VARS']['BE']['loginSecurityLevel'] === 'sha1') {
            $password = sha1($pass);
            if ($saltedPassword == $password) {
                $success = true;
            }
        } else {
            
                $objSalt = (new \TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory)->get($saltedPassword, 'BE');
            if (is_object($objSalt)) {
                $success = $objSalt->checkPassword((string) $pass, $saltedPassword);
            }
        }

        if ($success === true) {
            $pageUid = $data['uid'];
            $uriBuilder = $this->uriBuilder;
            $_SESSION['password-' . $pageUid . '-protect'] = 'Yes';

            $uri = $uriBuilder
                ->setTargetPageUid($pageUid)
                ->setCreateAbsoluteUri(true)
                ->build();
            return $this->responseFactory->createResponse(307)
                ->withHeader('Location', $uri);
        } else {
            $pageUid = $data['uid'];
            $uriBuilder = $this->uriBuilder;
            $uri = $uriBuilder
                ->setTargetPageUid($pageUid)
                ->setArguments(['type' => '88889', 'inavlid' => '1'])
                ->setCreateAbsoluteUri(true)
                ->build();
            return $this->responseFactory->createResponse(307)
                ->withHeader('Location', $uri);
        }
    }

    /**
     * action form
     *
     * @return void
     */
    public function formAction(): ResponseInterface
    {
        if ($_REQUEST['inavlid']) {
            $this->view->assign('inavlid', 1);
            
        }
        
        return $this->htmlResponse();
    }
}

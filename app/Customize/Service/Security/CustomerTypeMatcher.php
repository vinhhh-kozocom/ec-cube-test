<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Customize\Service\Security;

use Eccube\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class CustomerTypeMatcher implements RequestMatcherInterface
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    // if matches() func return false. The flow will be auto come to matches() again ???
    // if matches() func return TRUE. The flow will be normally
    // if not login presscess. matches() func return FALSE will be normally
    public function matches(Request $request): bool
    {
        if ($request->getRequestUri() == '/mypage/login' || $request->getRequestUri() == '/shopping/login') {
            if (!$request->isMethod('POST')) {
                // make sure the flash message is cleared in login form screen.
                $request->getSession()->getFlashBag()->clear();

                return false;
            }
            $flashBag = $request->getSession()->getFlashBag();
            if ($flashBag->has('eccube.front.login.request.error')) {
                return false;
            }

            $Customer = $this->customerRepository->findOneBy([
                'email' => $request->get('login_email'),
            ]);
            // if customer NOT exist, do nothing, the validate security will be operated by framework
            if (!$Customer) {
                return true;
            }
            $customerType = $Customer->getTypeCustomer() ?: 'common';

            if (
                ($_SERVER['SERVER_NAME'] === env('SALON_URL'))
                && ($customerType === 'common')
                || ($_SERVER['SERVER_NAME'] !== env('SALON_URL'))
                && ($customerType === 'salon')
            ) {
                $flashBag->add('eccube.front.login.request.error', trans('eccube.front.login.request.error'));

                return false;
            }
        }

        return true;
    }
}

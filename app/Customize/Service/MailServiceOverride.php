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

namespace Customize\Service;

use Customize\Common\Constants;
use Eccube\Entity\Customer;
use Eccube\Entity\MailHistory;
use Eccube\Entity\Order;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailServiceOverride extends \Eccube\Service\MailService
{
    /**
     * Send order mail.
     *
     * @param Order $Order 受注情報
     *
     * @return Email
     */
    public function sendOrderMail(Order $Order, ?Request $request = null, $isPending = false)
    {
        log_info('受注メール送信開始--MailServiceOverride');
        $isSalon = $_SERVER['SERVER_NAME'] === env('SALON_URL');
        $shopName = $isSalon ? Constants::SHOP_NAME_SALON : $this->BaseInfo->getShopName();
        // if locale not japanese
        if ($request && ($request->getLocale() != Constants::LOCALE_JAPAN)) {
            $mailTemplateId = $this->eccubeConfig['eccube_order_mail_template_locale_id'];
        } elseif ($isSalon) {
            // if salon page
            $salonFittingID = $this->eccubeConfig['eccube_order_mail_template_salon_fitting_id'];
            $salonDelivID = $this->eccubeConfig['eccube_order_mail_template_salon_deliv_id'];

            $mailTemplateId = ($Order->orderType == 'deliv_sal' ? $salonDelivID : $salonFittingID);
        } else {
            $templateId = $this->eccubeConfig['eccube_order_mail_template_id'];
            $templateDelivId = $this->eccubeConfig['eccube_order_mail_template_delivery_id'];

            $mailTemplateId = ($Order->orderType == 'visit' ? $templateId : $templateDelivId);
        }
        log_info('----orderTypeSendOrderMail----'.$Order->orderType.'ID:'.$Order->getId());

        $MailTemplate = $this->mailTemplateRepository->find($mailTemplateId ?? 1);
        $body = $this->twig->render($MailTemplate->getFileName(), [
            'Order' => $Order,
            'BaseInfo' => $this->BaseInfo,
            'Customer' => $Order->getCustomer(),
            'locale' => $request->getLocale(),
        ]);

        if ($request->getLocale() == Constants::LOCALE_JAPAN) {
            $subject = '['.$shopName.'] '.$MailTemplate->getMailSubject();
        } else {
            $subject = $request->getLocale() == 'en' ? 'Kimono Rental AKI: Thank you for your reservation' : '和服租賃 AKI：謝謝您的預訂';
        }

        // initing email and content -----------------------------------------------------------
        $message = (new Email())
            ->subject($subject)
            ->from(new Address($this->BaseInfo->getEmail01(), $shopName))
            ->to($this->convertRFCViolatingEmail($Order->getEmail()))
            ->bcc($this->BaseInfo->getEmail01(), $this->BaseInfo->getEmail02())
            ->replyTo($this->BaseInfo->getEmail03())
            ->returnPath($this->BaseInfo->getEmail04());

        // HTMLテンプレートが存在する場合
        $htmlFileName = $this->getHtmlTemplate($MailTemplate->getFileName());
        if (!is_null($htmlFileName)) {
            $htmlBody = $this->twig->render($htmlFileName, [
                'Order' => $Order,
                'BaseInfo' => $this->BaseInfo,
                'Customer' => $Order->getCustomer(),
                'locale' => $request->getLocale(),
            ]);

            $message
                ->text($body)
                ->html($htmlBody);
        } else {
            $message->text($body);
        }

        $event = new EventArgs(
            [
                'message' => $message,
                'Order' => $Order,
                'MailTemplate' => $MailTemplate,
                'BaseInfo' => $this->BaseInfo,
            ],
            null
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::MAIL_ORDER);

        // Sending email after init message ----------------------------------------
        if (!$isPending) {
            try {
                $this->mailer->send($message);
                log_info('----Send mail----');
            } catch (TransportExceptionInterface $e) {
                log_critical($e->getMessage());
            }
        }

        // Storing mail history -----------------------------------------------------
        $MailHistory = new MailHistory();
        $MailHistory->setMailSubject($message->getSubject())
            ->setMailBody($message->getTextBody())
            ->setOrder($Order)
            ->setSendDate(new \DateTime());

        // HTML用メールの設定
        $htmlBody = $message->getHtmlBody();
        if (!empty($htmlBody)) {
            $MailHistory->setMailHtmlBody($htmlBody);
        }
        if ($isPending) {
            $MailHistory->setIsPending(true);
            log_info('----Only save history, Not send mail----');
        }

        $this->mailHistoryRepository->save($MailHistory);

        log_info('受注メール送信完了');

        return $message;
    }

    /**
     * Send contact mail.
     *
     * @param $formData お問い合わせ内容
     */
    public function sendContactMail($formData)
    {
        $isSalon = $_SERVER['SERVER_NAME'] === env('SALON_URL');
        if ($isSalon) {
            return;
        }
        log_info('お問い合わせ受付メール送信開始');

        $MailTemplate = $this->mailTemplateRepository->find($this->eccubeConfig['eccube_contact_mail_template_id']);

        $body = $this->twig->render($MailTemplate->getFileName(), [
            'data' => $formData,
            'BaseInfo' => $this->BaseInfo,
            'isSalon' => $isSalon,
        ]);

        // 問い合わせ者にメール送信
        $message = (new Email())
            ->subject('['.$this->BaseInfo->getShopName().'] '.$MailTemplate->getMailSubject())
            ->from(new Address($this->BaseInfo->getEmail01(), $this->BaseInfo->getShopName()))
            ->to($this->convertRFCViolatingEmail($formData['email']))
            ->bcc($this->BaseInfo->getEmail02())
            ->replyTo($this->BaseInfo->getEmail02())
            ->returnPath($this->BaseInfo->getEmail04());

        // HTMLテンプレートが存在する場合
        $htmlFileName = $this->getHtmlTemplate($MailTemplate->getFileName());
        if (!is_null($htmlFileName)) {
            $htmlBody = $this->twig->render($htmlFileName, [
                'data' => $formData,
                'BaseInfo' => $this->BaseInfo,
                'isSalon' => $isSalon,
            ]);

            $message
                ->text($body)
                ->html($htmlBody);
        } else {
            $message->text($body);
        }

        $event = new EventArgs(
            [
                'message' => $message,
                'formData' => $formData,
                'BaseInfo' => $this->BaseInfo,
            ],
            null
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::MAIL_CONTACT);

        try {
            $this->mailer->send($message);
            log_info('お問い合わせ受付メール送信完了');
        } catch (TransportExceptionInterface $e) {
            log_critical($e->getMessage());
        }
    }

    public function sendCustomerWithdrawMail(Customer $Customer, string $email)
    {
        $isSalon = $_SERVER['SERVER_NAME'] === env('SALON_URL');
        if ($isSalon) {
            return;
        }
        log_info('退会手続き完了メール送信開始');

        $MailTemplate = $this->mailTemplateRepository->find($this->eccubeConfig['eccube_customer_withdraw_mail_template_id']);

        $body = $this->twig->render($MailTemplate->getFileName(), [
            'Customer' => $Customer,
            'BaseInfo' => $this->BaseInfo,
        ]);

        $message = (new Email())
            ->subject('['.$this->BaseInfo->getShopName().'] '.$MailTemplate->getMailSubject())
            ->from(new Address($this->BaseInfo->getEmail01(), $this->BaseInfo->getShopName()))
            ->to($this->convertRFCViolatingEmail($email))
            ->bcc($this->BaseInfo->getEmail01())
            ->replyTo($this->BaseInfo->getEmail03())
            ->returnPath($this->BaseInfo->getEmail04());

        // HTMLテンプレートが存在する場合
        $htmlFileName = $this->getHtmlTemplate($MailTemplate->getFileName());
        if (!is_null($htmlFileName)) {
            $htmlBody = $this->twig->render($htmlFileName, [
                'Customer' => $Customer,
                'BaseInfo' => $this->BaseInfo,
            ]);

            $message
                ->text($body)
                ->html($htmlBody);
        } else {
            $message->text($body);
        }

        $event = new EventArgs(
            [
                'message' => $message,
                'Customer' => $Customer,
                'BaseInfo' => $this->BaseInfo,
                'email' => $email,
            ],
            null
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::MAIL_CUSTOMER_WITHDRAW);

        try {
            $this->mailer->send($message);
            log_info('退会手続き完了メール送信完了');
        } catch (TransportExceptionInterface $e) {
            log_critical($e->getMessage());
        }
    }

    public function sendOrderMailCustom(Order $Order)
    {
        log_info('----sendOrderMailCustom-GetMailFromHistory----ID'.$Order?->getId());

        // find new mail history for this order
        $mailHistory = $this->mailHistoryRepository->findOneBy(
            [
                'Order' => $Order,
                'is_pending' => true,
            ]
        );

        if (!$mailHistory) {
            log_info('----No mail history found for order----ID'.$Order?->getId());

            return null;
        }

        $shopName = $this->BaseInfo->getShopName();

        // create email message with mail history content
        $message = (new Email())
            ->subject($mailHistory->getMailSubject())
            ->from(new Address($this->BaseInfo->getEmail01(), $shopName))
            ->to($this->convertRFCViolatingEmail($Order->getEmail()))
            ->bcc($this->BaseInfo->getEmail01(), $this->BaseInfo->getEmail02())
            ->replyTo($this->BaseInfo->getEmail03())
            ->returnPath($this->BaseInfo->getEmail04());

        // use mail history content
        if ($mailHistory->getMailHtmlBody()) {
            $message
                ->text($mailHistory->getMailBody())
                ->html($mailHistory->getMailHtmlBody());
        } else {
            $message->text($mailHistory->getMailBody());
        }

        // send email
        try {
            $this->mailer->send($message);
            log_info('----Send email success with mail history content for order: '.$Order->getId());

            $mailHistory->setIsPending(null);
        } catch (TransportExceptionInterface $e) {
            log_info('----Send email error: '.$e->getMessage());
        }

        return $message;
    }
}

<?php

namespace MultiMerch\Mail\Transport;

use Registry;

abstract class Factory
{
    /**
     * @param Registry $registry
     * @throws \MultiMerch\Mail\Exception\TransportException
     * @return MultiMerchMail
     */
    public static function create(Registry $registry)
    {
        $mailTransport = null;
        /** @var \Config $config */
        $config = $registry->get('config');
        switch ($config->get('config_mail_protocol')) {
            case "smtp":
                $mailTransport = new SMTP();
                break;
            default:
                $mailTransport = new SendMail();
                break;
        }
        if (!$mailTransport instanceof MultiMerchMail) {
            throw new \MultiMerch\Mail\Exception\TransportException('Could not create mail transport');
        }

        $mailTransport->parameter = $config->get('config_mail_parameter');
        $mailTransport->smtp_hostname = $config->get('config_mail_smtp_hostname');
        $mailTransport->smtp_username = $config->get('config_mail_smtp_username');
        $mailTransport->smtp_password = html_entity_decode($config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mailTransport->smtp_port = $config->get('config_mail_smtp_port');
        $mailTransport->smtp_timeout = $config->get('config_mail_smtp_timeout');
        $mailTransport->setFrom($config->get('config_email'));
        $mailTransport->setSender($config->get('config_name'));
        return $mailTransport;
    }
}

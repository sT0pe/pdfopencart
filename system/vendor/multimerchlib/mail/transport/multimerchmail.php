<?php

namespace MultiMerch\Mail\Transport;

use Mail;
use MultiMerch\Mail\Message;
use MultiMerch\ServiceLocator\ServiceLocatorAwareTrait;
use MultiMerch\ServiceLocator\ServiceLocatorAwareInterface;

class MultiMerchMail extends Mail implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $tplPath = 'catalog/view/theme/{theme}/msmail/';
    protected $layout = 'partials/layout.tpl';
    protected $header = 'partials/header.tpl';
    protected $footer = 'partials/footer.tpl';

    /** @var string Message specific content */
    protected $content;

    /**
     * Return sender defined in parent class
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    public function sendMails(Message\MessageCollection $msgs)
    {
        foreach ($msgs->getList() as $msg) {
            $this->sendMail($msg);
        }
    }

    public function sendMail(Message\Message $msg)
    {
        $this->initialize($msg); // set default values from transport to message

        $msg->beforeSend(); // perform before send action

        $this->initialize($msg); // update transport if $msg->beforeSend() made changes in message

        $html = $msg->getHtml();
        if ($html) {
            $this->html = $msg->getHtml();
        } else {
            /** @var \MultiMerch\View\Renderer\PhpRenderer $renderer */
            $renderer = $this->getServiceLocator()->get('phprenderer');

            $data = $msg->getData();
            $data['sender'] = $this->getSender();

            $header = $renderer->render($this->tplPath . $this->header, $data);
            $footer = $renderer->render($this->tplPath . $this->footer, $data);
            $content = $this->content;
            if (!$content && $msg->getTemplate()) {
                $content = $renderer->render($this->tplPath . $msg->getTemplate(), $data);
            }
            $tplData = array(
                'header' => $header,
                'footer' => $footer,
                'content' => $content,
            );
            $this->html = $renderer->render($this->tplPath . $this->layout, array_replace_recursive($msg->getData(), $tplData));
        }

        try {
            $this->send();
        } catch (\Exception $e) {
            $config = \MsLoader::getInstance()->getRegistry()->get('config');
            $log = new \Log($config->get('config_error_filename'));
            $log->write("MMERCH MAIL sending error: " . $e->getMessage() . "\n");
        }
    }

    protected function initialize(Message\Message $msg)
    {
        $to = $msg->getTo();
        if ($to) {
            $this->to = $to;
        }
        $msg->setTo($this->to);

        $from = $msg->getFrom();
        if ($from) {
            $this->from = $from;
        }
        $msg->setFrom($this->from);

        $sender = $msg->getSender();
        if ($sender) {
            $this->sender = $sender;
        }
        $msg->setSender($this->sender);

        $subject = $msg->getSubject();
        if ($subject) {
            $this->subject = $subject;
        }
        $msg->setSubject($this->subject);

        $text = $msg->getText();
        if ($text) {
            $this->text = $text;
        }
        $msg->setText($this->text);

        $content = $msg->getContent();
        if ($content) {
            $this->content = $content;
        }
        $msg->setContent($this->content);
    }
}
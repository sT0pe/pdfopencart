<?php

namespace MultiMerch\Mail\Message;

use MultiMerch\ServiceLocator\ServiceLocatorAwareTrait;
use MultiMerch\ServiceLocator\ServiceLocatorAwareInterface;

class Message implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $to;
    protected $from;
    protected $sender;
    protected $subject;
    protected $text;
    protected $html;

    /**
     * @var string
     *
     * Relative tpl to catalog/view/theme/{theme}/msmail/
     * Example example.tpl will be searched in catalog/view/theme/{theme}/msmail/example.tpl
     */
    protected $template;

    /** @var string Message specific content */
    protected $content;

    protected $data = array();

    /**
     * Called before message will be sent
     */
    public function beforeSend()
    {
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function setSender($sender)
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    public function setTemplate($file)
    {
        $this->template = $file;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function translate($key)
    {
        return $this->getServiceLocator()->get('Translator')->get($key);
    }
}
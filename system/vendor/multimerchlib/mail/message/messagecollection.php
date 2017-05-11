<?php

namespace MultiMerch\Mail\Message;

use Countable;

class MessageCollection implements Countable
{
    protected $msgs = array();

    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return int
     */
    public function count()
    {
        return count($this->msgs);
    }

    public function add(\MultiMerch\Mail\Message\Message $msg)
    {
        $this->msgs[] = $msg;
        return $this;
    }

    public function remove(\MultiMerch\Mail\Message\Message $removeMsg)
    {
        foreach ($this->msgs as $k => $msg) {
            // remove the same instance
            if ($removeMsg === $msg) {
                unset($this->msgs[$k]);
            }
        }
        return $this;
    }

    public function getList()
    {
        return $this->msgs;
    }
}
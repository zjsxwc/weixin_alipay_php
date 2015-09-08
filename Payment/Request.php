<?php
namespace WatcherHangzhouPayment\Payment;

abstract class Request
{
    protected $params = array();

    protected $options = array();

    public function __construct(array $options = null, array $params = null)
    {
        $this->options = $options;
        $this->params = $params;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    abstract public function form();
}

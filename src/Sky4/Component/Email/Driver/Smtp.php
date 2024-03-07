<?php

namespace Sky4\Component\Email\Driver;

use Exception as SplException,
    Sky4\Component\Email\Driver,
    Sky4\Component\Email\Smtp as SmtpClass,
    Sky4\Exception,
    Sky4\Helper\StringHelper;

class Smtp extends Driver {

    protected $authenticate = false;
    protected $host_name = 'localhost';
    protected $host_port = 25;
    protected $keep_alive = false;
    protected $result = false;
    protected $secure_protocol = '';
    protected $smtp = null;
    protected $timeout = 0;
    protected $user_name = '';
    protected $user_password = '';

    /**
     * @return SmtpClass
     */
    public function smtp() {
        if ($this->smtp === null) {
            $this->smtp = new SmtpClass($this);
        }
        return $this->smtp;
    }

    // -------------------------------------------------------------------------

    public function setAuthenticate($flag) {
        $this->authenticate = (bool) $flag;
        return $this;
    }

    public function setHostName($host_name) {
        $this->host_name = (string) $host_name;
        return $this;
    }

    public function setHostPort($host_port) {
        $this->host_port = (int) $host_port;
        return $this;
    }

    public function setKeepAlive($flag) {
        $this->keep_alive = (bool) $flag;
        return $this;
    }

    public function setResult($bool) {
        $this->result = (bool) $bool;
        return $this;
    }

    public function setSecureProtocol($secure_protocol) {
        $secure_protocol = StringHelper::toLower($secure_protocol);
        if (($secure_protocol === 'ssl') || ($secure_protocol === 'tls')) {
            $this->secure_protocol = $secure_protocol;
        }
        return $this;
    }

    public function setTimeout($timeout) {
        $this->timeout = (int) $timeout;
        return $this;
    }

    public function setUserName($user_name) {
        $this->user_name = (string) $user_name;
        return $this;
    }

    public function setUserPassword($user_password) {
        $this->user_password = (string) $user_password;
        return $this;
    }

    // -------------------------------------------------------------------------

    /**
     * @return Smtp
     */
    public function close() {
        if ($this->smtp()->connected()) {
            $this->smtp()->quit();
            $this->smtp()->closeConnection();
        }
        return $this;
    }

    /**
     * @return Smtp
     */
    public function connect() {
        if ($this->smtp()->connected()) {
            return true;
        }
        $hosts_names = explode(';', $this->host_name);
        try {
            $connected = false;
            foreach ($hosts_names as $host_name) {
                if (preg_match('/^(.+):([0-9]+)$/', $host_name, $matches)) {
                    $_host_name = (string) $matches[1];
                    $_host_port = (int) $matches[2];
                } else {
                    $_host_name = $host_name;
                    $_host_port = $this->host_port;
                }
                if ($this->secure_protocol === 'ssl') {
                    $_host_name = 'ssl://' . $_host_name;
                }
                if ($this->smtp()->connect($_host_name, $_host_port, $this->timeout)) {
                    if ($this->smtp()->helo($_host_name)) {
                        if ($this->secure_protocol === 'tls') {
                            if (!$this->smtp()->startTls()) {
                                // @todo Добавить вывод логов.
                                throw new Exception('Ошибка smtp [startTls]');
                            }
                            if (!$this->smtp()->helo($host_name)) {
                                // @todo Добавить вывод логов.
                                throw new Exception('Ошибка smtp [helo]');
                            }
                        }
                        if ($this->authenticate && !$this->smtp()->authLogin($this->user_name, $this->user_password)) {
                            // @todo Добавить вывод логов.
                            throw new Exception('Ошибка smtp [authLogin]');
                        }
                        $connected = true;
                        break;
                    } else {
                        // @todo Добавить вывод логов.
                        throw new Exception('Ошибка smtp [helo]');
                    }
                }
            }
            if (!$connected) {
                // @todo Добавить вывод логов.
                throw new Exception('Ошибка smtp [connect]');
            }
        } catch (SplException $e) {
            //$this->smtp()->rset();
            throw $e;
        }
        return $this;
    }

    public function getResult() {
        return $this->result;
    }

    /**
     * @return Smtp
     */
    public function send() {
        $this->setResult(false);
        $this->connect();
        try {
            if ($this->smtp()->mailFrom($this->email()->getFromAddress())) {
                $bad_recipients = [];
                foreach ($this->email()->getTo() as $to) {
                    if (isset($to[0])) {
                        if (!$this->smtp()->rcptTo($to[0])) {
                            $bad_recipients[] = $to[0];
                        }
                    }
                }
                foreach ($this->email()->getCc() as $cc) {
                    if (isset($cc[0])) {
                        if (!$this->smtp()->rcptTo($cc[0])) {
                            $bad_recipients[] = $cc[0];
                        }
                    }
                }
                foreach ($this->email()->getBcc() as $bcc) {
                    if (isset($bcc[0])) {
                        if (!$this->smtp()->rcptTo($bcc[0])) {
                            $bad_recipients[] = $bcc[0];
                        }
                    }
                }

                if (!count($bad_recipients) && $this->smtp()->data($this->email()->assembleHeaders() . $this->smtp()->getCrlf() . $this->email()->assembleBody())) {
                    $this->setResult(true);
                }
                if ($this->keep_alive) {
                    $this->smtp()->rset();
                } else {
                    $this->close();
                }
            } else {
                // @todo Добавить вывод логов.
                throw new Exception('Ошибка smtp [mailFrom]');
            }
        } catch (SplException $e) {
            //$this->smtp()->rset();
            throw $e;
        }
        return $this;
    }

}

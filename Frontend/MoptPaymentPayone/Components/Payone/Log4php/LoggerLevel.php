<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * Defines the minimum set of levels recognized by the system, that is
 * <i>OFF</i>, <i>FATAL</i>, <i>ERROR</i>,
 * <i>WARN</i>, <i>INFO</i>, <i>DEBUG</i> and
 * <i>ALL</i>.
 *
 * <p>The <i>LoggerLevel</i> class may be subclassed to define a larger
 * level set.</p>
 *
 * @version $Revision$
 * @package log4php
 * @since 0.5
 */
class Payone_Log4php_LoggerLevel
{
    
    const OFF = 2147483647;
    const FATAL = 50000;
    const ERROR = 40000;
    const WARN = 30000;
    const INFO = 20000;
    const DEBUG = 10000;
    const TRACE = 5000;
    const ALL = -2147483647;

    /** Integer level value. */
    private $level;
    
    /** Contains a list of instantiated levels. */
    private static $levelMap;

    /** String representation of the level. */
    private $levelStr;

    /**
     * Equivalent syslog level.
     * @var integer
     */
    private $syslogEquivalent;

    /**
     * Constructor
     *
     * @param integer $level
     * @param string $levelStr
     * @param integer $syslogEquivalent
     */
    private function __construct($level, $levelStr, $syslogEquivalent)
    {
        $this->level = $level;
        $this->levelStr = $levelStr;
        $this->syslogEquivalent = $syslogEquivalent;
    }

    /**
     * Compares two logger levels.
     *
     * @param LoggerLevels $other
     * @return boolean
     */
    public function equals($other)
    {
        if ($other instanceof Payone_Log4php_LoggerLevel) {
            if ($this->level == $other->level) {
                return true;
            }
        } else {
            return false;
        }
        return false;
    }
    
    /**
     * Returns an Off Level
     * @return Payone_Log4php_LoggerLevel
     */
    public static function getLevelOff()
    {
        if (!isset(self::$levelMap[Payone_Log4php_LoggerLevel::OFF])) {
            self::$levelMap[Payone_Log4php_LoggerLevel::OFF] = new Payone_Log4php_LoggerLevel(Payone_Log4php_LoggerLevel::OFF, 'OFF', LOG_ALERT);
        }
        return self::$levelMap[Payone_Log4php_LoggerLevel::OFF];
    }

    /**
     * Returns a Fatal Level
     * @return Payone_Log4php_LoggerLevel
     */
    public static function getLevelFatal()
    {
        if (!isset(self::$levelMap[Payone_Log4php_LoggerLevel::FATAL])) {
            self::$levelMap[Payone_Log4php_LoggerLevel::FATAL] = new Payone_Log4php_LoggerLevel(Payone_Log4php_LoggerLevel::FATAL, 'FATAL', LOG_ALERT);
        }
        return self::$levelMap[Payone_Log4php_LoggerLevel::FATAL];
    }
    
    /**
     * Returns an Error Level
     * @return Payone_Log4php_LoggerLevel
     */
    public static function getLevelError()
    {
        if (!isset(self::$levelMap[Payone_Log4php_LoggerLevel::ERROR])) {
            self::$levelMap[Payone_Log4php_LoggerLevel::ERROR] = new Payone_Log4php_LoggerLevel(Payone_Log4php_LoggerLevel::ERROR, 'ERROR', LOG_ERR);
        }
        return self::$levelMap[Payone_Log4php_LoggerLevel::ERROR];
    }
    
    /**
     * Returns a Warn Level
     * @return Payone_Log4php_LoggerLevel
     */
    public static function getLevelWarn()
    {
        if (!isset(self::$levelMap[Payone_Log4php_LoggerLevel::WARN])) {
            self::$levelMap[Payone_Log4php_LoggerLevel::WARN] = new Payone_Log4php_LoggerLevel(Payone_Log4php_LoggerLevel::WARN, 'WARN', LOG_WARNING);
        }
        return self::$levelMap[Payone_Log4php_LoggerLevel::WARN];
    }

    /**
     * Returns an Info Level
     * @return Payone_Log4php_LoggerLevel
     */
    public static function getLevelInfo()
    {
        if (!isset(self::$levelMap[Payone_Log4php_LoggerLevel::INFO])) {
            self::$levelMap[Payone_Log4php_LoggerLevel::INFO] = new Payone_Log4php_LoggerLevel(Payone_Log4php_LoggerLevel::INFO, 'INFO', LOG_INFO);
        }
        return self::$levelMap[Payone_Log4php_LoggerLevel::INFO];
    }

    /**
     * Returns a Debug Level
     * @return Payone_Log4php_LoggerLevel
     */
    public static function getLevelDebug()
    {
        if (!isset(self::$levelMap[Payone_Log4php_LoggerLevel::DEBUG])) {
            self::$levelMap[Payone_Log4php_LoggerLevel::DEBUG] = new Payone_Log4php_LoggerLevel(Payone_Log4php_LoggerLevel::DEBUG, 'DEBUG', LOG_DEBUG);
        }
        return self::$levelMap[Payone_Log4php_LoggerLevel::DEBUG];
    }
    
    /**
     * Returns a Trace Level
     * @return Payone_Log4php_LoggerLevel
     */
    public static function getLevelTrace()
    {
        if (!isset(self::$levelMap[Payone_Log4php_LoggerLevel::TRACE])) {
            self::$levelMap[Payone_Log4php_LoggerLevel::TRACE] = new Payone_Log4php_LoggerLevel(Payone_Log4php_LoggerLevel::TRACE, 'TRACE', LOG_DEBUG);
        }
        return self::$levelMap[Payone_Log4php_LoggerLevel::TRACE];
    }

    /**
     * Returns an All Level
     * @return Payone_Log4php_LoggerLevel
     */
    public static function getLevelAll()
    {
        if (!isset(self::$levelMap[Payone_Log4php_LoggerLevel::ALL])) {
            self::$levelMap[Payone_Log4php_LoggerLevel::ALL] = new Payone_Log4php_LoggerLevel(Payone_Log4php_LoggerLevel::ALL, 'ALL', LOG_DEBUG);
        }
        return self::$levelMap[Payone_Log4php_LoggerLevel::ALL];
    }
    
    /**
     * Return the syslog equivalent of this priority as an integer.
     * @return integer
     */
    public function getSyslogEquivalent()
    {
        return $this->syslogEquivalent;
    }

    /**
     * Returns <i>true</i> if this level has a higher or equal
     * level than the level passed as argument, <i>false</i>
     * otherwise.
     *
     * @param Payone_Log4php_LoggerLevel $other
     * @return boolean
     */
    public function isGreaterOrEqual($other)
    {
        return $this->level >= $other->level;
    }

    /**
     * Returns the string representation of this level.
     * @return string
     */
    public function toString()
    {
        return $this->levelStr;
    }
    
    /**
     * Returns the string representation of this level.
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Returns the integer representation of this level.
     * @return integer
     */
    public function toInt()
    {
        return $this->level;
    }

    /**
     * Convert the input argument to a level. If the conversion fails, then
     * this method returns the provided default level.
     *
     * @param mixed $arg The value to convert to level.
     * @param Payone_Log4php_LoggerLevel $default Value to return if conversion is not possible.
     * @return Payone_Log4php_LoggerLevel
     */
    public static function toLevel($arg, $defaultLevel = null)
    {
        if (is_int($arg)) {
            switch ($arg) {
                case self::ALL:
                    return self::getLevelAll();
                case self::TRACE:
                    return self::getLevelTrace();
                case self::DEBUG:
                    return self::getLevelDebug();
                case self::INFO:
                    return self::getLevelInfo();
                case self::WARN:
                    return self::getLevelWarn();
                case self::ERROR:
                    return self::getLevelError();
                case self::FATAL:
                    return self::getLevelFatal();
                case self::OFF:
                    return self::getLevelOff();
                default:
                    return $defaultLevel;
            }
        } else {
            switch (strtoupper($arg)) {
                case 'ALL':
                    return self::getLevelAll();
                case 'TRACE':
                    return self::getLevelTrace();
                case 'DEBUG':
                    return self::getLevelDebug();
                case 'INFO':
                    return self::getLevelInfo();
                case 'WARN':
                    return self::getLevelWarn();
                case 'ERROR':
                    return self::getLevelError();
                case 'FATAL':
                    return self::getLevelFatal();
                case 'OFF':
                    return self::getLevelOff();
                default:
                    return $defaultLevel;
            }
        }
    }
}

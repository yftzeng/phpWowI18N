<?php
/**
 * PHP WowI18N
 *
 * PHP version 5
 *
 * @category Wow
 * @package  WowI18N
 * @author   Tzeng, Yi-Feng <yftzeng@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/yftzeng/phpWowI18N
 */

namespace Wow\I18N;

/**
 * PHP WowI18N
 *
 * @category Wow
 * @package  WowI18N
 * @author   Tzeng, Yi-Feng <yftzeng@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/yftzeng/phpWowI18N
 */
class WowI18N
{

    protected $langFilePath = 'lang';
    protected $langFileCachePath = 'langcache';
    protected $fallbackLang = 'en-US';
    protected $langPrefix = 'L';
    protected $currentLang = null;
    protected $sectionSeperator = '_';
    protected $langVariable = 'lang';

    protected $currentTimeZone = null;
    protected $systemTimeZone = null;

    protected $currencyMinFractionDigits = 0;
    protected $currencyMaxFractionDigits = 0;

    /**
     * @param string $langFilePath      language file path
     * @param string $langFileCachePath language file cache path
     * @param string $fallbackLang      fallbackLang
     * @param string $forcedLang        forced lang
     * @param string $langVariable      language variable name
     * @param string $langPrefix        language variable prefix name
     * @param string $systemTimeZone    system timezone
     *
     * @comment construct
     *
     * @return void
     */
    public function __construct(
        $langFilePath = null, $langFileCachePath = null, $fallbackLang = null, $forcedLang = null,
        $langVariable = null, $langPrefix = null, $systemTimeZone = null
    ) {
        if ($langFilePath !== null) {
            $this->langFilePath = $langFilePath;
        }

        if ($langFileCachePath !== null) {
            $this->langFileCachePath = $langFileCachePath;
        }

        if ($fallbackLang !== null) {
            $this->fallbackLang = $fallbackLang;
        }

        if ($langVariable !== null) {
            $this->langVariable = $langVariable;
        }

        if ($langPrefix !== null) {
            $this->langPrefix = $langPrefix;
        }

        if ($systemTimeZone !== null) {
            $this->systemTimeZone = $systemTimeZone;
        }
        if ($this->systemTimeZone === null) {
            $this->systemTimeZone = date_default_timezone_get();
        }

        if ($forcedLang !== null) {
            $this->currentLang = $forcedLang;
        } else {
            $this->currentLang = $this->getCurrentLang();
        }

        $_langFile = $this->langFilePath . '/lang_' . $this->currentLang . '.ini';
        if (!file_exists($_langFile)) {
            throw new \RuntimeException('No language file was found.');
        }

        $_langFileCache = $this->langFileCachePath . '/lang_' . $this->currentLang . '.cache';

        if (!file_exists($_langFileCache) || filemtime($_langFileCache) < filemtime($_langFile)) {
            $_config_file = parse_ini_file($_langFile, true);

            $compiled  = '<?php class ' . $this->langPrefix . " {\n";
            $compiled .= $this->compile($_config_file);
            $compiled .= 'public static function __callStatic($string, $args) {' . "\n";
            $compiled .= 'vprintf(constant("self::" . $string), $args);' . "\n";
            $compiled .= "}\n}";

            if (!is_dir($this->langFileCachePath)) {
                mkdir($this->langFileCachePath, 0777, true);
            }
            file_put_contents($_langFileCache, $compiled);
            chmod($_langFileCache, 0777);
        }

        include_once $_langFileCache;
    }

    /**
     * @comment Get current locale
     *
     * @return string
     */
    public function getCurrentLang()
    {
        if (isset($_GET[$this->langVariable])) {
            return $_GET[$this->langVariable];
        }

        if (isset($_SESSION[$this->langVariable])) {
            return $_SESSION[$this->langVariable];
        }

        if (isset($_COOKIE[$this->langVariable])) {
            return $_COOKIE[$this->langVariable];
        }

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }

        return $this->fallbackLang;
    }

    /**
     * @param string $config config
     * @param string $prefix prefix
     *
     * @comment compile config file
     *
     * @return string
     */
    protected function compile($config, $prefix = '')
    {
        $code = '';
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $code .= $this->compile($value, $prefix . $key . $this->sectionSeperator);
            } else {
                $code .= 'const ' . $prefix . $key . ' = \'' . str_replace('\'', '\\\'', $value) . "';\n";
            }
        }
        return $code;
    }

    /**
     * @param string $tz timezone
     *
     * @comment Set current timezone
     *
     * @return void
     */
    public function setTimeZone($tz)
    {
        $this->currentTimeZone = $tz;
    }

    /**
     * @comment Get current timezone
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->currentTimeZone;
    }

    /**
     * @param int $value value
     *
     * @comment Set value of min fraction digits of currency
     *
     * @return void
     */
    public function setCurrencyMinFractionDigits($value)
    {
        $this->currencyMinFractionDigits = $value;
    }

    /**
     * @param int $value value
     *
     * @comment Set value of max fraction digits of currency
     *
     * @return void
     */
    public function setCurrencyMaxFractionDigits($value)
    {
        $this->currencyMaxFractionDigits = $value;
    }

    /**
     * @param string $monetary                  monetary
     * @param int    $currencyMinFractionDigits min fraction digits of currency
     * @param int    $currencyMaxFractionDigits max fraction digits of currency
     *
     * @comment Get number format of locale
     *
     * @return string
     */
    public function N($monetary, $currencyMinFractionDigits = null, $currencyMaxFractionDigits = null)
    {
        return $this->_numberFormat(0, $monetary, $currencyMinFractionDigits, $currencyMaxFractionDigits);
    }

    /**
     * @param string $monetary                  monetary
     * @param int    $currencyMinFractionDigits min fraction digits of currency
     * @param int    $currencyMaxFractionDigits max fraction digits of currency
     *
     * @comment Get monetary format of locale
     *
     * @return string
     */
    public function M($monetary, $currencyMinFractionDigits = null, $currencyMaxFractionDigits = null)
    {
        return $this->_numberFormat(1, $monetary, $currencyMinFractionDigits, $currencyMaxFractionDigits);
    }

    /**
     * @param bool   $is_currency               is currency or not
     * @param string $monetary                  monetary
     * @param int    $currencyMinFractionDigits min fraction digits of currency
     * @param int    $currencyMaxFractionDigits max fraction digits of currency
     *
     * @comment Get NumberFormatter
     *
     * @return string
     */
    private function _numberFormat($is_currency, $monetary, $currencyMinFractionDigits = null, $currencyMaxFractionDigits = null)
    {
        if ($is_currency) {
            $_format = new \NumberFormatter($this->currentLang, \NumberFormatter::CURRENCY);
        } else {
            $_format = new \NumberFormatter($this->currentLang, \NumberFormatter::DECIMAL);
        }

        if ($currencyMinFractionDigits !== null) {
            $_format->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $currencyMinFractionDigits);
        } else {
            $_format->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $this->currencyMinFractionDigits);
        }

        if ($currencyMaxFractionDigits !== null) {
            $_format->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $currencyMaxFractionDigits);
        } else {
            $_format->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $this->currencyMaxFractionDigits);
        }

        return $_format->format($monetary);
    }

    /**
     * @param string $dateTimeString DateTimeString
     *
     * @comment Get DateTimeString of current locale
     *
     * @return string
     */
    public function T($dateTimeString, $timeZone = null)
    {
        if (\DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString) === false && DateTime::createFromFormat('Y-m-d', $dateTimeString) === false) {
            error_log('dateTimeString is not valid.');
            return;
        }

        if ($timeZone != null) {
            $dateTimeString
                = new \DateTime($dateTimeString, new \DateTimeZone($this->systemTimeZone));
            return $dateTimeString
                ->setTimezone(new \DateTimeZone($timeZone))
                ->format('Y-m-d H:i:s');
        }
        else {
            if ($this->currentTimeZone !== null && $this->systemTimeZone !== $this->currentTimeZone) {
                $dateTimeString
                    = new \DateTime($dateTimeString, new \DateTimeZone($this->systemTimeZone));
                return $dateTimeString
                    ->setTimezone(new \DateTimeZone($this->currentTimeZone))
                    ->format('Y-m-d H:i:s');
            }
        }

        return $dateTimeString;
    }

}

<?php

define('I18N_LANGFILEPATH', 'lang');
define('I18N_LANGFILECACHEPATH', 'langcache');
define('I18N_FALLBACKLANG', 'en-US');

$forcedLang = 'zh-TW';

include __DIR__.'/../src/Wow/I18N/WowI18N.php';

use Wow\I18N\WowI18N;

$I = new WowI18N(I18N_LANGFILEPATH, I18N_LANGFILECACHEPATH, I18N_FALLBACKLANG, $forcedLang);

// force set current lang
$I->setCurrencyMinFractionDigits(5);
$I->setCurrencyMaxFractionDigits(7);

// get number
echo $I->N(12345.12345)."\n";

// get montary
echo $I->M(12345.12345)."\n";

// change timezone
$I->setTimeZone('America/New_York');
$timezone = '2015-12-25 14:15:11';
echo 'Current time: '.$timezone."\n";
echo 'Current timezone: '.$I->T($timezone)."\n";

// trans
echo L::DEMO."\n";

<?php

define('I18N_LANGFILEPATH', 'lang');
define('I18N_LANGFILECACHEPATH', 'langcache');
define('I18N_FALLBACKLANG', 'en-US');

$forcedLang = 'zh-TW';

include __DIR__.'/../src/Wow/I18N/WowI18N.php';

use Wow\I18N\WowI18N;

$I = new WowI18N(I18N_LANGFILEPATH, I18N_LANGFILECACHEPATH, I18N_FALLBACKLANG, $forcedLang);

echo $I->T(date('Y-m-d H:i:s'), 'UTC');
echo "\n";
echo $I->T(date('Y-m-d H:i:s'), 'Asia/Taipei');
echo "\n";
exit;

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
echo 'Current timezone (UTC): '.$I->T($timezone, 'UTC')."\n";
echo 'Current timezone (Taipei): '.$I->T($timezone, 'Asia/Taipei')."\n";
$I->setTimeZone('UTC');
echo 'Current timezone (UTC): '.$I->T($timezone)."\n";
$I->setTimeZone('Asia/Taipei');
echo 'Current timezone (Taipei): '.$I->T($timezone)."\n";

// trans
echo L::DEMO."\n";

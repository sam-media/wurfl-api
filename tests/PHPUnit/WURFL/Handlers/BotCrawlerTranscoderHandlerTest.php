<?php
/**
 * test case
 */
/**
 *  test case.
 */
class WURFL_Hanlders_BotCrawlerTranscoderHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider botCrawlerTranscoderUserAgentsProvider
     */
    public function testShouldHandleKnownBots($userAgent)
    {
        WURFL_Handlers_Utils::reset();
        $normalizer = new WURFL_Request_UserAgentNormalizer_Null();
        $context = new WURFL_Context(null);
        $handler = new WURFL_Handlers_BotCrawlerTranscoderHandler($context, $normalizer);
        $found = $handler->canHandle($userAgent);
        $this->assertTrue($found);
    }

    public function botCrawlerTranscoderUserAgentsProvider()
    {
        return array(
            array("CCBot/1.0 (+http://www.commoncrawl.org/bot.html)"),
            array("DoCoMo/2.0 N905i(c100;TB;W24H16) (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)"),
            array("Gaisbot/3.0+(robot06@gais.cs.ccu.edu.tw;+http://gais.cs.ccu.edu.tw/robot.php)"),
            array("Gigabot/3.0 (http://www.gigablast.com/spider.html)"),
            array("kalooga/KaloogaBot (Kalooga; http://www.kalooga.com/info.html?page=crawler)"),
            array("LijitSpider/Nutch-0.9 (Reports crawler; http://www.lijit.com/robot/crawler; info(a)lijit(d)com)"),
            array("MLBot (www.metadatalabs.com/mlbot)"),
            array("Mozilla/5.0 (compatible; BecomeBot/3.0; +http://www.become.com/site_owners.html)"),
            array("Mozilla/5.0 (compatible; DBLBot/1.0; +http://www.dontbuylists.com/)"),
            array("Mozilla/5.0 (compatible; discobot/1.0; +http://discoveryengine.com/discobot.html)"),
            array("Mozilla/5.0 (compatible; DotBot/1.1; http://www.dotnetdotcom.org/, crawler@dotnetdotcom.org)"),
            array("Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)"),
            array("Mozilla/5.0 (compatible; heritrix/1.15.1-200807172326 +http://www.accelobot.com)"),
            array("Mozilla/5.0 (compatible; MJ12bot/v1.2.2; http://www.majestic12.co.uk/bot.php?+)"),
            array("Mozilla/5.0 (compatible; MojeekBot/2.0; http://www.mojeek.com/bot.html)"),
            array("Mozilla/5.0 (Twiceler-0.9 http://www.cuil.com/twiceler/robot.html)"),
            array("MSMOBOT/1.1 (+http://search.msn.com/msnbot.htm)"),
            array("msnbot-media/1.0 (+http://search.msn.com/msnbot.htm)"),
            array("msnbot-media/1.1 (+http://search.msn.com/msnbot.htm)"),
            array("msnbot/1.1 (+http://search.msn.com/msnbot.htm)"),
            array("MSNBOT_Mobile MSMOBOT Mozilla/2.0 (compatible; MSIE 4.02; Windows CE; Default)/1.1 (+http://search.msn.com/msnbot.htm)"),
            array("MSRBOT (http://research.microsoft.com/research/sv/msrbot/)"),
            array("Nokia6820/2.0 (4.83) Profile/MIDP-1.0 Configuration/CLDC-1.0 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)"),
            array("PDFBot crawler@pdfind.com"),
            array("SAMSUNG-SGH-E250/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 UP.Browser/6.2.3.3.c.1.101 (GUI) MMP/2.0 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)"),
            array("Snapbot/1.0 (Snap Shots, +http://www.snap.com)"),
            array("SonyEricssonK800i/R1ED Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1 (Mediobot/1.0 +http://bot.medio.com)"),
            array("T-Mobile Dash Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; Smartphone; 320x240; MSNBOT-MOBILE/1.1; +http://search.msn.com/msnbot.htm)"),
            array("T-Mobile Dash Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; Smartphone; 320x240;) MSNBOT-MOBILE/1.1 (+http://search.msn.com/msnbot.htm)"),
            array("WAP_Browser/5.0 (compatible; YodaoBot/1.0; http://www.youdao.com/help/webmaster/spider/; )"),
            array("Yanga WorldSearch Bot v1.1/beta (http://www.yanga.co.uk/)"),
        );
    }
}

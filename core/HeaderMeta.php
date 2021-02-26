<?php


namespace Core;


class HeaderMeta
{
    private $htmlAr;
    private $CompatibilityMode = false;
    private $charset = "utf-8";
    private $viewport = "width=device-width, minimum-scale=1, initial-scale=1, maximum-scale=1, user-scalable=0";
    private $keyWords = "";
    private $pageDescription = "";
    private $pageTitle = "";
    private $siteName = "";
    private $type = "";
    private $pageUrl = "";
    private $imageUrl = "/assets/images/site-image.php"; // Default Image
    private $imageWidth = "";
    private $imageHeight = "";
    private $appId = "";
    private $feedUrl = "";
    private $twitterCard = "";
    private $twitterCreator = "";
    private $febIconUrl = "";
    private $pageId = "";
    private $analyticsId = "";

    public function __construct()
    {
        global $SoftInfo, $Route;
        $SoftInfoData = $SoftInfo->getData();

        $this->keyWords = trim((string)$SoftInfoData->software->keywords);
        $this->pageDescription = trim((string)$SoftInfoData->software->description);
        $this->siteName = (string)$SoftInfoData->software->title;
        $this->type = (string)$SoftInfoData->software->type;
        $this->feedUrl = (string)$SoftInfoData->software->feedUrl;

        $this->pageTitle = $Route->getPageTitle();
        $this->pageUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->imageUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/cloud-uploads/" . getDefaultDomain() . "/def-site-image.png";

        $this->appId = (string)$SoftInfoData->fb->app_id;
        $this->pageId = (string)$SoftInfoData->fb->page_id;

        $this->twitterCard = (string)$SoftInfoData->twitter->card;
        $this->twitterCreator = (string)$SoftInfoData->twitter->creator;

        $this->analyticsId = $SoftInfoData->google->analyticsId;
        $this->gTagsCode = $SoftInfoData->google->gTagsCode;
    }

    function getMeta(): string
    {
        //-- FebIcon
        $febIcon = str_replace("{domain}", getDefaultDomain(), getSoftInfo()->software->febicon);

        if (is_file($febIcon)) {
            $this->htmlAr['febicon'] = "<link rel=\"shortcut icon\" href=\"/$febIcon\" type=\"image/x-icon\">";
        } else {
            $this->htmlAr['febicon'] = "<link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\">";
        }

        //-- View Port
        if ($this->CompatibilityMode == false) {
            $this->htmlAr['viewport_CompatibilityMode'] = "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"/>";
        }

        if ($this->charset) {
            $this->htmlAr['viewport_charset'] = "<meta charset=\"" . $this->charset . "\">";
        }

        if ($this->viewport) {
            $this->htmlAr['viewport_viewport'] = "<meta name=\"viewport\" content=\"" . $this->viewport . "\"/>";
        }

        //--SEO
        if ($this->keyWords) {
            $this->htmlAr['seo_keyWords'] = "<meta name=\"keywords\" content=\"" . $this->keyWords . "\"/>";
        }

        if ($this->pageDescription) {
            $this->htmlAr['seo_pageDescription'] = "<meta name=\"description\" content=\"" . $this->pageDescription . "\"/>";
        }

        //--FB
        if ($this->pageTitle) {
            $this->htmlAr['fb_pageTitle'] = "<meta property=\"og:title\" content=\"" . $this->getFullTitle() . "\"/>";
        }

        if ($this->siteName) {
            $this->htmlAr['fb_siteName'] = "<meta property=\"og:site_name\" content=\"" . $this->siteName . "\"/>";
        }

        if ($this->pageDescription) {
            $this->htmlAr['fb_pageDescription'] = "<meta property=\"og:description\" content=\"" . $this->pageDescription . "\"/>";
        }

        if ($this->type) {
            $this->htmlAr['fb_type'] = "<meta property=\"og:type\" content=\"" . $this->type . "\"/>";
        }

        if ($this->pageUrl) {
            $this->htmlAr['fb_pageUrl'] = "<meta property=\"og:url\" content=\"" . $this->pageUrl . "\"/>";
        }

        if ($this->imageUrl) {
            $this->htmlAr['fb_imageUrl'] = "<meta property=\"og:image\" content=\"" . $this->imageUrl . "\"/>";
        }

        if ($this->imageWidth) {
            $this->htmlAr['fb_imageWidth'] = "<meta property=\"og:image:width\" content=\"" . $this->imageWidth . "\"/>";
        }

        if ($this->imageHeight) {
            $this->htmlAr['fb_imageHeight'] = "<meta property=\"og:image:height\" content=\"" . $this->imageHeight . "\"/>";
        }

        if ($this->appId) {
            $this->htmlAr['fb_appId'] = "<meta property=\"fb:app_id\" content=\"" . $this->appId . "\"/>";
        }

        if ($this->appId) {
            $this->htmlAr['fb_pageId'] = "<meta property=\"fb:pages\" content=\"" . $this->pageId . "\"/>";
        }


        //--Twitter
        if ($this->twitterCard) {
            $this->htmlAr['twitter_twitterCard'] = "<meta name=\"twitter:card\" content=\"" . $this->twitterCard . "\"/>";
        }
        if ($this->pageUrl) {
            $this->htmlAr['twitter_pageUrl'] = "<meta name=\"twitter:site\" content=\"" . $this->pageUrl . "\"/>";
        }
        if ($this->twitterCreator) {
            $this->htmlAr['twitter_twitterCreator'] = "<meta name=\"twitter:creator\" content=\"" . $this->twitterCreator . "\"/>";
        }
        if ($this->pageUrl) {
            $this->htmlAr['twitter_pageUrl'] = "<meta name=\"twitter:url\" content=\"" . $this->pageUrl . "\"/>";
        }
        if ($this->pageTitle) {
            $this->htmlAr['twitter_pageTitle'] = "<meta name=\"twitter:title\" content=\"" . $this->getFullTitle() . "\"/>";
        }
        if ($this->pageDescription) {
            $this->htmlAr['twitter_pageDescription'] = "<meta name=\"twitter:description\" content=\"" . $this->pageDescription . "\"/>";
        }

        //--Canonical
        if ($this->pageUrl) {
            $this->htmlAr['canonical_pageUrl'] = "<link rel=\"canonical\" href=\"" . $this->pageUrl . "\"><!--canonical link-->";
        }
        if ($this->febIconUrl) {
            $this->htmlAr['canonical_febIconUrl'] = "<link rel=\"shortcut icon\" href=\"" . $this->febIconUrl . "\"/><!--shortcut icon -->";
        }
        if ($this->febIconUrl) {
            $this->htmlAr['canonical_febIconUrl'] = "<link rel=\"icon\" type=\"image/ico\" href=\" . $this->febIconUrl . \"/>";
        }
        if ($this->feedUrl) {
            $this->htmlAr['canonical_feedUrl'] = "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"" . $this->feedUrl . "\"/><!--rss -->";
        }

        $this->setAnalyticsId();

        return implode("\n", array_filter($this->htmlAr));
    }

    public function setAnalyticsId()
    {
        if ($this->analyticsId) {
            $this->htmlAr['analyticsId'] = "
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src=\"https://www.googletagmanager.com/gtag/js?id=" . $this->analyticsId . "\"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
            
              gtag('config', '" . $this->analyticsId . "');
            </script>
            ";
        }
    }

    public function setCompatibilityMode(bool $CompatibilityMode): void
    {
        $this->CompatibilityMode = $CompatibilityMode;
    }

    public function setPageTitle(string $title): HeaderMeta
    {
        $this->pageTitle = $title;
        $this->keyWords = implode(", ", array_filter(explode(" ", $title)));
        return $this;
    }

    public function setType(string $type): HeaderMeta
    {
        $this->type = $type;
        return $this;
    }

    public function setImageUrl(string $imageUrl): HeaderMeta
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function setDescription(string $pageDescription): HeaderMeta
    {
        $this->pageDescription = $pageDescription;
        return $this;
    }

    public function setKeyWord(string $keyWord): HeaderMeta
    {
        $this->keyWords = $keyWord;
        return $this;
    }

    public function getFullTitle($glue = " || "): string
    {
        return implode($glue, array_filter([
            $this->siteName,
            $this->pageTitle
        ]));
    }
}
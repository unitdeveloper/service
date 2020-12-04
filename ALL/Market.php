<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\ALL;

use zetsoft\service\market\Address;
use zetsoft\service\market\AddressTest;
use zetsoft\service\market\AdminFarhod;
use zetsoft\service\market\AdminMainStatic;
use zetsoft\service\market\AdminStatistic;
use zetsoft\service\market\Banner;
use zetsoft\service\market\Brand;
use zetsoft\service\market\BrandTest;
use zetsoft\service\market\Breadcrumb;
use zetsoft\service\market\CallCenter;
use zetsoft\service\market\Cart;
use zetsoft\service\market\Catalog;
use zetsoft\service\market\Category;
use zetsoft\service\market\CategoryOver;
use zetsoft\service\market\CategoryOveride;
use zetsoft\service\market\CategoryTest;
use zetsoft\service\market\CategoryXolmat;
use zetsoft\service\market\Category_old;
use zetsoft\service\market\Company;
use zetsoft\service\market\CompanyStat;
use zetsoft\service\market\Coupon;
use zetsoft\service\market\Courier;
use zetsoft\service\market\Cpas;
use zetsoft\service\market\Element;
use zetsoft\service\market\Fetch;
use zetsoft\service\market\Fetch1;
use zetsoft\service\market\Filter;
use zetsoft\service\market\FilterForm;
use zetsoft\service\market\FilterM;
use zetsoft\service\market\GeneratorBarCodes;
use zetsoft\service\market\GuestStatistic;
use zetsoft\service\market\Mirshod;
use zetsoft\service\market\Offer;
use zetsoft\service\market\OfferTest;
use zetsoft\service\market\Operator;
use zetsoft\service\market\OperatorStats;
use zetsoft\service\market\Order;
use zetsoft\service\market\OrderNurbek;
use zetsoft\service\market\OrderOld;
use zetsoft\service\market\OrderXolmat;
use zetsoft\service\market\Overview;
use zetsoft\service\market\phpSerial;
use zetsoft\service\market\Product;
use zetsoft\service\market\ProductM;
use zetsoft\service\market\ProductTest;
use zetsoft\service\market\ProductUMID;
use zetsoft\service\market\Question;
use zetsoft\service\market\Report;
use zetsoft\service\market\ReportAcceptance;
use zetsoft\service\market\ReportAcceptanceData;
use zetsoft\service\market\ReportBahodir;
use zetsoft\service\market\ReportBroadcast;
use zetsoft\service\market\ReportCatalog;
use zetsoft\service\market\ReportCdk;
use zetsoft\service\market\ReportCdkOLdv1;
use zetsoft\service\market\ReportCdkOldv2;
use zetsoft\service\market\ReportCollections;
use zetsoft\service\market\ReportCourier;
use zetsoft\service\market\ReportCourierOld;
use zetsoft\service\market\ReportCourierold1;
use zetsoft\service\market\ReportNodirbek;
use zetsoft\service\market\ReportNurbek;
use zetsoft\service\market\ReportOld1;
use zetsoft\service\market\ReportReasons;
use zetsoft\service\market\Reports1;
use zetsoft\service\market\Reports2;
use zetsoft\service\market\Reports3;
use zetsoft\service\market\ReportsNurbek;
use zetsoft\service\market\ReportsOld;
use zetsoft\service\market\ReportSource;
use zetsoft\service\market\ReportsSukhrob;
use zetsoft\service\market\ReportsXolmat;
use zetsoft\service\market\ReportXolmat;
use zetsoft\service\market\Review;
use zetsoft\service\market\SellerStatistic;
use zetsoft\service\market\Session;
use zetsoft\service\market\Shipment;
use zetsoft\service\market\Stats;
use zetsoft\service\market\Subscribe;
use zetsoft\service\market\trackingUrlGenerator;
use zetsoft\service\market\Wares;
use zetsoft\service\market\Wish;
use yii\base\Component;



/**
 *
* @property Address $address
* @property AddressTest $addressTest
* @property AdminFarhod $adminFarhod
* @property AdminMainStatic $adminMainStatic
* @property AdminStatistic $adminStatistic
* @property Banner $banner
* @property Brand $brand
* @property BrandTest $brandTest
* @property Breadcrumb $breadcrumb
* @property CallCenter $callCenter
* @property Cart $cart
* @property Catalog $catalog
* @property Category $category
* @property CategoryOver $categoryOver
* @property CategoryOveride $categoryOveride
* @property CategoryTest $categoryTest
* @property CategoryXolmat $categoryXolmat
* @property Category_old $category_old
* @property Company $company
* @property CompanyStat $companyStat
* @property Coupon $coupon
* @property Courier $courier
* @property Cpas $cpas
* @property Element $element
* @property Fetch $fetch
* @property Fetch1 $fetch1
* @property Filter $filter
* @property FilterForm $filterForm
* @property FilterM $filterM
* @property GeneratorBarCodes $generatorBarCodes
* @property GuestStatistic $guestStatistic
* @property Mirshod $mirshod
* @property Offer $offer
* @property OfferTest $offerTest
* @property Operator $operator
* @property OperatorStats $operatorStats
* @property Order $order
* @property OrderNurbek $orderNurbek
* @property OrderOld $orderOld
* @property OrderXolmat $orderXolmat
* @property Overview $overview
* @property phpSerial $phpSerial
* @property Product $product
* @property ProductM $productM
* @property ProductTest $productTest
* @property ProductUMID $productUMID
* @property Question $question
* @property Report $report
* @property ReportAcceptance $reportAcceptance
* @property ReportAcceptanceData $reportAcceptanceData
* @property ReportBahodir $reportBahodir
* @property ReportBroadcast $reportBroadcast
* @property ReportCatalog $reportCatalog
* @property ReportCdk $reportCdk
* @property ReportCdkOLdv1 $reportCdkOLdv1
* @property ReportCdkOldv2 $reportCdkOldv2
* @property ReportCollections $reportCollections
* @property ReportCourier $reportCourier
* @property ReportCourierOld $reportCourierOld
* @property ReportCourierold1 $reportCourierold1
* @property ReportNodirbek $reportNodirbek
* @property ReportNurbek $reportNurbek
* @property ReportOld1 $reportOld1
* @property ReportReasons $reportReasons
* @property Reports1 $reports1
* @property Reports2 $reports2
* @property Reports3 $reports3
* @property ReportsNurbek $reportsNurbek
* @property ReportsOld $reportsOld
* @property ReportSource $reportSource
* @property ReportsSukhrob $reportsSukhrob
* @property ReportsXolmat $reportsXolmat
* @property ReportXolmat $reportXolmat
* @property Review $review
* @property SellerStatistic $sellerStatistic
* @property Session $session
* @property Shipment $shipment
* @property Stats $stats
* @property Subscribe $subscribe
* @property trackingUrlGenerator $trackingUrlGenerator
* @property Wares $wares
* @property Wish $wish

 */

class Market extends Component
{

    
    private $_address;
    private $_addressTest;
    private $_adminFarhod;
    private $_adminMainStatic;
    private $_adminStatistic;
    private $_banner;
    private $_brand;
    private $_brandTest;
    private $_breadcrumb;
    private $_callCenter;
    private $_cart;
    private $_catalog;
    private $_category;
    private $_categoryOver;
    private $_categoryOveride;
    private $_categoryTest;
    private $_categoryXolmat;
    private $_category_old;
    private $_company;
    private $_companyStat;
    private $_coupon;
    private $_courier;
    private $_cpas;
    private $_element;
    private $_fetch;
    private $_fetch1;
    private $_filter;
    private $_filterForm;
    private $_filterM;
    private $_generatorBarCodes;
    private $_guestStatistic;
    private $_mirshod;
    private $_offer;
    private $_offerTest;
    private $_operator;
    private $_operatorStats;
    private $_order;
    private $_orderNurbek;
    private $_orderOld;
    private $_orderXolmat;
    private $_overview;
    private $_phpSerial;
    private $_product;
    private $_productM;
    private $_productTest;
    private $_productUMID;
    private $_question;
    private $_report;
    private $_reportAcceptance;
    private $_reportAcceptanceData;
    private $_reportBahodir;
    private $_reportBroadcast;
    private $_reportCatalog;
    private $_reportCdk;
    private $_reportCdkOLdv1;
    private $_reportCdkOldv2;
    private $_reportCollections;
    private $_reportCourier;
    private $_reportCourierOld;
    private $_reportCourierold1;
    private $_reportNodirbek;
    private $_reportNurbek;
    private $_reportOld1;
    private $_reportReasons;
    private $_reports1;
    private $_reports2;
    private $_reports3;
    private $_reportsNurbek;
    private $_reportsOld;
    private $_reportSource;
    private $_reportsSukhrob;
    private $_reportsXolmat;
    private $_reportXolmat;
    private $_review;
    private $_sellerStatistic;
    private $_session;
    private $_shipment;
    private $_stats;
    private $_subscribe;
    private $_trackingUrlGenerator;
    private $_wares;
    private $_wish;

    
    public function getAddress()
    {
        if ($this->_address === null)
            $this->_address = new Address();

        return $this->_address;
    }
    

    public function getAddressTest()
    {
        if ($this->_addressTest === null)
            $this->_addressTest = new AddressTest();

        return $this->_addressTest;
    }
    

    public function getAdminFarhod()
    {
        if ($this->_adminFarhod === null)
            $this->_adminFarhod = new AdminFarhod();

        return $this->_adminFarhod;
    }
    

    public function getAdminMainStatic()
    {
        if ($this->_adminMainStatic === null)
            $this->_adminMainStatic = new AdminMainStatic();

        return $this->_adminMainStatic;
    }
    

    public function getAdminStatistic()
    {
        if ($this->_adminStatistic === null)
            $this->_adminStatistic = new AdminStatistic();

        return $this->_adminStatistic;
    }
    

    public function getBanner()
    {
        if ($this->_banner === null)
            $this->_banner = new Banner();

        return $this->_banner;
    }
    

    public function getBrand()
    {
        if ($this->_brand === null)
            $this->_brand = new Brand();

        return $this->_brand;
    }
    

    public function getBrandTest()
    {
        if ($this->_brandTest === null)
            $this->_brandTest = new BrandTest();

        return $this->_brandTest;
    }
    

    public function getBreadcrumb()
    {
        if ($this->_breadcrumb === null)
            $this->_breadcrumb = new Breadcrumb();

        return $this->_breadcrumb;
    }
    

    public function getCallCenter()
    {
        if ($this->_callCenter === null)
            $this->_callCenter = new CallCenter();

        return $this->_callCenter;
    }
    

    public function getCart()
    {
        if ($this->_cart === null)
            $this->_cart = new Cart();

        return $this->_cart;
    }
    

    public function getCatalog()
    {
        if ($this->_catalog === null)
            $this->_catalog = new Catalog();

        return $this->_catalog;
    }
    

    public function getCategory()
    {
        if ($this->_category === null)
            $this->_category = new Category();

        return $this->_category;
    }
    

    public function getCategoryOver()
    {
        if ($this->_categoryOver === null)
            $this->_categoryOver = new CategoryOver();

        return $this->_categoryOver;
    }
    

    public function getCategoryOveride()
    {
        if ($this->_categoryOveride === null)
            $this->_categoryOveride = new CategoryOveride();

        return $this->_categoryOveride;
    }
    

    public function getCategoryTest()
    {
        if ($this->_categoryTest === null)
            $this->_categoryTest = new CategoryTest();

        return $this->_categoryTest;
    }
    

    public function getCategoryXolmat()
    {
        if ($this->_categoryXolmat === null)
            $this->_categoryXolmat = new CategoryXolmat();

        return $this->_categoryXolmat;
    }
    

    public function getCategory_old()
    {
        if ($this->_category_old === null)
            $this->_category_old = new Category_old();

        return $this->_category_old;
    }
    

    public function getCompany()
    {
        if ($this->_company === null)
            $this->_company = new Company();

        return $this->_company;
    }
    

    public function getCompanyStat()
    {
        if ($this->_companyStat === null)
            $this->_companyStat = new CompanyStat();

        return $this->_companyStat;
    }
    

    public function getCoupon()
    {
        if ($this->_coupon === null)
            $this->_coupon = new Coupon();

        return $this->_coupon;
    }
    

    public function getCourier()
    {
        if ($this->_courier === null)
            $this->_courier = new Courier();

        return $this->_courier;
    }
    

    public function getCpas()
    {
        if ($this->_cpas === null)
            $this->_cpas = new Cpas();

        return $this->_cpas;
    }
    

    public function getElement()
    {
        if ($this->_element === null)
            $this->_element = new Element();

        return $this->_element;
    }
    

    public function getFetch()
    {
        if ($this->_fetch === null)
            $this->_fetch = new Fetch();

        return $this->_fetch;
    }
    

    public function getFetch1()
    {
        if ($this->_fetch1 === null)
            $this->_fetch1 = new Fetch1();

        return $this->_fetch1;
    }
    

    public function getFilter()
    {
        if ($this->_filter === null)
            $this->_filter = new Filter();

        return $this->_filter;
    }
    

    public function getFilterForm()
    {
        if ($this->_filterForm === null)
            $this->_filterForm = new FilterForm();

        return $this->_filterForm;
    }
    

    public function getFilterM()
    {
        if ($this->_filterM === null)
            $this->_filterM = new FilterM();

        return $this->_filterM;
    }
    

    public function getGeneratorBarCodes()
    {
        if ($this->_generatorBarCodes === null)
            $this->_generatorBarCodes = new GeneratorBarCodes();

        return $this->_generatorBarCodes;
    }
    

    public function getGuestStatistic()
    {
        if ($this->_guestStatistic === null)
            $this->_guestStatistic = new GuestStatistic();

        return $this->_guestStatistic;
    }
    

    public function getMirshod()
    {
        if ($this->_mirshod === null)
            $this->_mirshod = new Mirshod();

        return $this->_mirshod;
    }
    

    public function getOffer()
    {
        if ($this->_offer === null)
            $this->_offer = new Offer();

        return $this->_offer;
    }
    

    public function getOfferTest()
    {
        if ($this->_offerTest === null)
            $this->_offerTest = new OfferTest();

        return $this->_offerTest;
    }
    

    public function getOperator()
    {
        if ($this->_operator === null)
            $this->_operator = new Operator();

        return $this->_operator;
    }
    

    public function getOperatorStats()
    {
        if ($this->_operatorStats === null)
            $this->_operatorStats = new OperatorStats();

        return $this->_operatorStats;
    }
    

    public function getOrder()
    {
        if ($this->_order === null)
            $this->_order = new Order();

        return $this->_order;
    }
    

    public function getOrderNurbek()
    {
        if ($this->_orderNurbek === null)
            $this->_orderNurbek = new OrderNurbek();

        return $this->_orderNurbek;
    }
    

    public function getOrderOld()
    {
        if ($this->_orderOld === null)
            $this->_orderOld = new OrderOld();

        return $this->_orderOld;
    }
    

    public function getOrderXolmat()
    {
        if ($this->_orderXolmat === null)
            $this->_orderXolmat = new OrderXolmat();

        return $this->_orderXolmat;
    }
    

    public function getOverview()
    {
        if ($this->_overview === null)
            $this->_overview = new Overview();

        return $this->_overview;
    }
    

    public function getPhpSerial()
    {
        if ($this->_phpSerial === null)
            $this->_phpSerial = new phpSerial();

        return $this->_phpSerial;
    }
    

    public function getProduct()
    {
        if ($this->_product === null)
            $this->_product = new Product();

        return $this->_product;
    }
    

    public function getProductM()
    {
        if ($this->_productM === null)
            $this->_productM = new ProductM();

        return $this->_productM;
    }
    

    public function getProductTest()
    {
        if ($this->_productTest === null)
            $this->_productTest = new ProductTest();

        return $this->_productTest;
    }
    

    public function getProductUMID()
    {
        if ($this->_productUMID === null)
            $this->_productUMID = new ProductUMID();

        return $this->_productUMID;
    }
    

    public function getQuestion()
    {
        if ($this->_question === null)
            $this->_question = new Question();

        return $this->_question;
    }
    

    public function getReport()
    {
        if ($this->_report === null)
            $this->_report = new Report();

        return $this->_report;
    }
    

    public function getReportAcceptance()
    {
        if ($this->_reportAcceptance === null)
            $this->_reportAcceptance = new ReportAcceptance();

        return $this->_reportAcceptance;
    }
    

    public function getReportAcceptanceData()
    {
        if ($this->_reportAcceptanceData === null)
            $this->_reportAcceptanceData = new ReportAcceptanceData();

        return $this->_reportAcceptanceData;
    }
    

    public function getReportBahodir()
    {
        if ($this->_reportBahodir === null)
            $this->_reportBahodir = new ReportBahodir();

        return $this->_reportBahodir;
    }
    

    public function getReportBroadcast()
    {
        if ($this->_reportBroadcast === null)
            $this->_reportBroadcast = new ReportBroadcast();

        return $this->_reportBroadcast;
    }
    

    public function getReportCatalog()
    {
        if ($this->_reportCatalog === null)
            $this->_reportCatalog = new ReportCatalog();

        return $this->_reportCatalog;
    }
    

    public function getReportCdk()
    {
        if ($this->_reportCdk === null)
            $this->_reportCdk = new ReportCdk();

        return $this->_reportCdk;
    }
    

    public function getReportCdkOLdv1()
    {
        if ($this->_reportCdkOLdv1 === null)
            $this->_reportCdkOLdv1 = new ReportCdkOLdv1();

        return $this->_reportCdkOLdv1;
    }
    

    public function getReportCdkOldv2()
    {
        if ($this->_reportCdkOldv2 === null)
            $this->_reportCdkOldv2 = new ReportCdkOldv2();

        return $this->_reportCdkOldv2;
    }
    

    public function getReportCollections()
    {
        if ($this->_reportCollections === null)
            $this->_reportCollections = new ReportCollections();

        return $this->_reportCollections;
    }
    

    public function getReportCourier()
    {
        if ($this->_reportCourier === null)
            $this->_reportCourier = new ReportCourier();

        return $this->_reportCourier;
    }
    

    public function getReportCourierOld()
    {
        if ($this->_reportCourierOld === null)
            $this->_reportCourierOld = new ReportCourierOld();

        return $this->_reportCourierOld;
    }
    

    public function getReportCourierold1()
    {
        if ($this->_reportCourierold1 === null)
            $this->_reportCourierold1 = new ReportCourierold1();

        return $this->_reportCourierold1;
    }
    

    public function getReportNodirbek()
    {
        if ($this->_reportNodirbek === null)
            $this->_reportNodirbek = new ReportNodirbek();

        return $this->_reportNodirbek;
    }
    

    public function getReportNurbek()
    {
        if ($this->_reportNurbek === null)
            $this->_reportNurbek = new ReportNurbek();

        return $this->_reportNurbek;
    }
    

    public function getReportOld1()
    {
        if ($this->_reportOld1 === null)
            $this->_reportOld1 = new ReportOld1();

        return $this->_reportOld1;
    }
    

    public function getReportReasons()
    {
        if ($this->_reportReasons === null)
            $this->_reportReasons = new ReportReasons();

        return $this->_reportReasons;
    }
    

    public function getReports1()
    {
        if ($this->_reports1 === null)
            $this->_reports1 = new Reports1();

        return $this->_reports1;
    }
    

    public function getReports2()
    {
        if ($this->_reports2 === null)
            $this->_reports2 = new Reports2();

        return $this->_reports2;
    }
    

    public function getReports3()
    {
        if ($this->_reports3 === null)
            $this->_reports3 = new Reports3();

        return $this->_reports3;
    }
    

    public function getReportsNurbek()
    {
        if ($this->_reportsNurbek === null)
            $this->_reportsNurbek = new ReportsNurbek();

        return $this->_reportsNurbek;
    }
    

    public function getReportsOld()
    {
        if ($this->_reportsOld === null)
            $this->_reportsOld = new ReportsOld();

        return $this->_reportsOld;
    }
    

    public function getReportSource()
    {
        if ($this->_reportSource === null)
            $this->_reportSource = new ReportSource();

        return $this->_reportSource;
    }
    

    public function getReportsSukhrob()
    {
        if ($this->_reportsSukhrob === null)
            $this->_reportsSukhrob = new ReportsSukhrob();

        return $this->_reportsSukhrob;
    }
    

    public function getReportsXolmat()
    {
        if ($this->_reportsXolmat === null)
            $this->_reportsXolmat = new ReportsXolmat();

        return $this->_reportsXolmat;
    }
    

    public function getReportXolmat()
    {
        if ($this->_reportXolmat === null)
            $this->_reportXolmat = new ReportXolmat();

        return $this->_reportXolmat;
    }
    

    public function getReview()
    {
        if ($this->_review === null)
            $this->_review = new Review();

        return $this->_review;
    }
    

    public function getSellerStatistic()
    {
        if ($this->_sellerStatistic === null)
            $this->_sellerStatistic = new SellerStatistic();

        return $this->_sellerStatistic;
    }
    

    public function getSession()
    {
        if ($this->_session === null)
            $this->_session = new Session();

        return $this->_session;
    }
    

    public function getShipment()
    {
        if ($this->_shipment === null)
            $this->_shipment = new Shipment();

        return $this->_shipment;
    }
    

    public function getStats()
    {
        if ($this->_stats === null)
            $this->_stats = new Stats();

        return $this->_stats;
    }
    

    public function getSubscribe()
    {
        if ($this->_subscribe === null)
            $this->_subscribe = new Subscribe();

        return $this->_subscribe;
    }
    

    public function getTrackingUrlGenerator()
    {
        if ($this->_trackingUrlGenerator === null)
            $this->_trackingUrlGenerator = new trackingUrlGenerator();

        return $this->_trackingUrlGenerator;
    }
    

    public function getWares()
    {
        if ($this->_wares === null)
            $this->_wares = new Wares();

        return $this->_wares;
    }
    

    public function getWish()
    {
        if ($this->_wish === null)
            $this->_wish = new Wish();

        return $this->_wish;
    }
    


}

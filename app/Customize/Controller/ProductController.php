<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Customize\Controller;

use Customize\Common\Constants;
use Customize\Entity\ProductDescription;
use Customize\Repository\OrderDetailAdditionalInfoRepository;
use Customize\Repository\ProductRepository;
use Eccube\Common\EccubeConfig;
use Eccube\Controller\AbstractController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Master\ProductListMaxType;
use Eccube\Form\Type\Master\ProductListOrderByType;
use Eccube\Form\Type\SearchProductType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Eccube\Repository\Master\ProductListMaxRepository;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ProductController extends AbstractController
{
    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * @var CustomerFavoriteProductRepository
     */
    protected $customerFavoriteProductRepository;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var AuthenticationUtils
     */
    protected $helper;

    /**
     * @var ProductListMaxRepository
     */
    protected $productListMaxRepository;

    private $title = '';

    private $orderDetailAdditionalInfoRepository;

    /**
     * ProductController constructor.
     *
     * @param PurchaseFlow $cartPurchaseFlow
     * @param CustomerFavoriteProductRepository $customerFavoriteProductRepository
     * @param CartService $cartService
     * @param ProductRepository $productRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param AuthenticationUtils $helper
     * @param ProductListMaxRepository $productListMaxRepository
     */
    public function __construct(
        PurchaseFlow $cartPurchaseFlow,
        CustomerFavoriteProductRepository $customerFavoriteProductRepository,
        CartService $cartService,
        ProductRepository $productRepository,
        BaseInfoRepository $baseInfoRepository,
        AuthenticationUtils $helper,
        ProductListMaxRepository $productListMaxRepository,
        OrderDetailAdditionalInfoRepository $orderDetailAdditionalInfoRepository,
        EccubeConfig $eccubeConfig,
    ) {
        $this->purchaseFlow = $cartPurchaseFlow;
        $this->customerFavoriteProductRepository = $customerFavoriteProductRepository;
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->helper = $helper;
        $this->productListMaxRepository = $productListMaxRepository;
        $this->orderDetailAdditionalInfoRepository = $orderDetailAdditionalInfoRepository;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * 商品一覧画面.
     *
     * @Route("/products/list", name="product_list", methods={"GET"})
     *
     * @Template("Product/list.twig")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        // Doctrine SQLFilter
        if ($this->BaseInfo->isOptionNostockHidden()) {
            $this->entityManager->getFilters()->enable('option_nostock_hidden');
        }

        // handleRequestは空のqueryの場合は無視するため
        if ($request->getMethod() === 'GET') {
            $request->query->set('pageno', $request->query->get('pageno', ''));
        }

        // searchForm
        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createNamedBuilder('', SearchProductType::class);

        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_INDEX_INITIALIZE);

        /* @var $searchForm \Symfony\Component\Form\FormInterface */
        $searchForm = $builder->getForm();

        $searchForm->handleRequest($request);

        // paginator
        $searchData = $searchForm->getData();

        // Get list category custom
        $categories = [];
        for ($i = 0; $i <= 4; $i++) {
            $paramName = 'category_id_'.$i;
            if ($request->query->has($paramName)) {
                $categories[] = $request->query->get($paramName);
            }
        }
        $date = $request->query->get('date');
        $qb = $this->productRepository->getQueryBuilderBySearchData($searchData, $this->entityManager, $categories, $date);

        $event = new EventArgs(
            [
                'searchData' => $searchData,
                'qb' => $qb,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_INDEX_SEARCH);
        $searchData = $event->getArgument('searchData');

        $query = $qb->getQuery()
            ->useResultCache(true, $this->eccubeConfig['eccube_result_cache_lifetime_short']);

        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $query,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            !empty($searchData['disp_number']) ? $searchData['disp_number']->getId() : $this->productListMaxRepository->findOneBy([], ['sort_no' => 'ASC'])->getId()
        );

        // Choice number product to show page
        $dispNumberForm = $this->formFactory->createNamed('disp_number', ProductListMaxType::class, null, [
            'method' => 'GET',
            'empty_data' => null,
            'required' => false,
            'label' => '表示件数',
            'allow_extra_fields' => true,
        ])->handleRequest($request);

        $ids = [];
        foreach ($pagination as $Product) {
            $ids[] = $Product->getId();
        }
        $ProductsAndClassCategories = $this->productRepository->findProductsWithSortedClassCategories($ids, 'p.id');

        // Order by form
        $orderByForm = $this->formFactory->createNamed('orderby', ProductListOrderByType::class, null, [
            'method' => 'GET',
            'empty_data' => null,
            'required' => false,
            'label' => '表示件数',
            'allow_extra_fields' => true,
        ])->handleRequest($request);

        // addCart form
        $forms = [];
        foreach ($pagination as $Product) {
            if (!isset($ProductsAndClassCategories[$Product->getId()])) {
                continue;
            }
            /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
            $builder = $this->formFactory->createNamedBuilder(
                '',
                AddCartType::class,
                null,
                [
                    'product' => $ProductsAndClassCategories[$Product->getId()],
                    'allow_extra_fields' => true,
                ]
            );
            $addCartForm = $builder->getForm();

            $forms[$Product->getId()] = $addCartForm->createView();
        }

        $Category = $searchForm->get('category_id')->getData();

        return [
            'subtitle' => $this->getPageTitle($searchData),
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
            'forms' => $forms,
            'Category' => $Category,
            'disp_number_form' => $dispNumberForm->createView(),
            'order_by_form' => $orderByForm->createView(),
        ];
    }

    /**
     * 商品詳細画面.
     *
     * @Route("/products/discontinued", name="products_discontinued", methods={"GET"})
     *
     * @Template("Product/discontinued.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function discontinued(Request $request)
    {
    }

    /**
     * 商品詳細画面.
     *
     * @Route("/products/detail/{id}", name="product_detail", methods={"GET"}, requirements={"id" = "\d+"})
     *
     * @Template("Product/detail.twig")
     *
     * @ParamConverter("Product", options={"repository_method" = "findWithSortedClassCategories"})
     *
     * @param Request $request
     * @param Product $Product
     *
     * @return array
     */
    public function detail(Request $request, Product $Product)
    {
        $result = $this->validateLocale($request, $Product);
        // if product language is different from locale url
        if ($result['isPass'] == false) {
            return $this->redirectToRoute('product_detail_locale', [
                'id' => $Product->getId(),
                '_locale' => $result['locale'],
            ]);
        }

        if (!$this->checkVisibility($Product)) {
            return $this->redirectToRoute('products_discontinued');
        }

        if ($_SERVER['SERVER_NAME'] === env('SALON_URL')) {
            $notShowTMV = $this->entityManager->getRepository(ProductCategory::class)->createQueryBuilder('p')
                ->where('p.product_id = :product_id and p.category_id in ('.$this->eccubeConfig['category_not_show_tmv'].')')
                ->setParameter('product_id', $Product->getId())
                ->getQuery()
                ->getResult();

            if ($notShowTMV) {
                throw new NotFoundHttpException();
            }
        }

        $builder = $this->formFactory->createNamedBuilder(
            '',
            AddCartType::class,
            null,
            [
                'product' => $Product,
                'id_add_product_id' => false,
            ]
        );

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Product' => $Product,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_DETAIL_INITIALIZE);

        $is_favorite = false;
        if ($this->isGranted('ROLE_USER')) {
            $Customer = $this->getUser();
            $is_favorite = $this->customerFavoriteProductRepository->isFavorite($Customer, $Product);
        }

        $productNotAvailableDates = [];
        if ($_SERVER['SERVER_NAME'] === env('SALON_URL')) {
            $productNotAvailableDates = [
                'normal' => $this->orderDetailAdditionalInfoRepository->getListNotAvailableDates($this->entityManager, $Product->getId()),
                'fitting' => $this->orderDetailAdditionalInfoRepository->getListNotAvailableDates($this->entityManager, $Product->getId(), 'fitting'),
                'flex' => $this->orderDetailAdditionalInfoRepository->getListNotAvailableDates($this->entityManager, $Product->getId(), 'flex'),
            ];

            $startDetail = strpos($Product->getDescriptionDetail(), '<div');
            if ($startDetail !== false) {
                $Product->setDescriptionDetail(substr($Product->getDescriptionDetail(), 0, $startDetail));
            }
        }

        $productDescription = $this->entityManager->getRepository(ProductDescription::class)->findOneBy([
            'product_id' => $Product->getId(),
        ]);

        return [
            'title' => $this->title,
            'subtitle' => $Product->getName(),
            'form' => $builder->getForm()->createView(),
            'Product' => $Product,
            'is_favorite' => $is_favorite,
            'productNotAvailableDates' => json_encode($productNotAvailableDates),
            'productDescription' => $productDescription,
        ];
    }

    /**
     * 商品詳細画面.
     *
     * @Route("/{_locale}/products/detail/{id}", name="product_detail_locale", methods={"GET"}, requirements={"id" = "\d+"})
     *
     * @Template("Product/detail.twig")
     *
     * @ParamConverter("Product", options={"repository_method" = "findWithSortedClassCategories"})
     *
     * @param Request $request
     * @param Product $Product
     *
     * @return array
     */
    public function detailLocale(Request $request, Product $Product)
    {
        if ($this->eccubeConfig['salon_domain'] == $_SERVER['SERVER_NAME']) {
            return $this->redirectToRoute('homepage');
        }

        $result = $this->validateLocale($request, $Product);
        // if product language is different from locale url
        if ($result['isPass'] == false) {
            return $this->redirectToRoute('product_detail_locale', [
                'id' => $Product->getId(),
                '_locale' => $result['locale'],
            ]);
        }

        if ($request->getLocale() == Constants::LOCALE_JAPAN) {
            return $this->redirectToRoute('product_detail', [
                'id' => $Product->getId(),
            ]);
        }

        return $this->detail($request, $Product);
    }

    /**
     * お気に入り追加.
     *
     * @Route("/products/add_favorite/{id}", name="product_add_favorite", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function addFavorite(Request $request, Product $Product)
    {
        $this->checkVisibility($Product);

        $event = new EventArgs(
            [
                'Product' => $Product,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_FAVORITE_ADD_INITIALIZE);

        if ($this->isGranted('ROLE_USER')) {
            $Customer = $this->getUser();
            $this->customerFavoriteProductRepository->addFavorite($Customer, $Product);
            $this->session->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());

            $event = new EventArgs(
                [
                    'Product' => $Product,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_FAVORITE_ADD_COMPLETE);

            return $this->redirectToRoute('product_detail', ['id' => $Product->getId()]);
        } else {
            // 非会員の場合、ログイン画面を表示
            //  ログイン後の画面遷移先を設定
            $this->setLoginTargetPath($this->generateUrl('product_add_favorite', ['id' => $Product->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
            $this->session->getFlashBag()->set('eccube.add.favorite', true);

            $event = new EventArgs(
                [
                    'Product' => $Product,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_FAVORITE_ADD_COMPLETE);

            return $this->redirectToRoute('mypage_login');
        }
    }

    /**
     * カートに追加.
     *
     * @Route("/products/add_cart/{id}", name="product_add_cart", methods={"POST"}, requirements={"id" = "\d+"})
     */
    public function addCart(Request $request, Product $Product)
    {
        // エラーメッセージの配列
        $errorMessages = [];
        if (!$this->checkVisibility($Product)) {
            throw new NotFoundHttpException();
        }

        $builder = $this->formFactory->createNamedBuilder(
            '',
            AddCartType::class,
            null,
            [
                'product' => $Product,
                'id_add_product_id' => false,
                'allow_extra_fields' => true,
            ]
        );

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Product' => $Product,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_CART_ADD_INITIALIZE);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        $form->handleRequest($request);
        if (!$form->isValid()) {
            throw new NotFoundHttpException();
        }

        $addCartData = $form->getData();

        log_info(
            'カート追加処理開始',
            [
                'product_id' => $Product->getId(),
                'product_class_id' => $addCartData['product_class_id'],
                'quantity' => $addCartData['quantity'],
            ]
        );

        // カートへ追加
        $this->cartService->addProductOverride($addCartData['product_class_id'], $addCartData['quantity'], $form);

        // 明細の正規化
        $Carts = $this->cartService->getCarts();
        foreach ($Carts as $Cart) {
            $result = $this->purchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));
            // 復旧不可のエラーが発生した場合は追加した明細を削除.
            if ($result->hasError()) {
                $this->cartService->removeProduct($addCartData['product_class_id']);
                foreach ($result->getErrors() as $error) {
                    $errorMessages[] = $error->getMessage();
                }
            }
            foreach ($result->getWarning() as $warning) {
                $errorMessages[] = $warning->getMessage();
            }
        }

        $this->cartService->save();
        log_info(
            'カート追加処理完了',
            [
                'product_id' => $Product->getId(),
                'product_class_id' => $addCartData['product_class_id'],
                'quantity' => $addCartData['quantity'],
            ]
        );

        $event = new EventArgs(
            [
                'form' => $form,
                'Product' => $Product,
            ],
            $request
        );

        if (empty($errorMessages)) {
            $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_CART_ADD_COMPLETE);
        }

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        if ($request->isXmlHttpRequest()) {
            // ajaxでのリクエストの場合は結果をjson形式で返す。

            // 初期化
            $messages = [];

            if (empty($errorMessages)) {
                // エラーが発生していない場合
                $done = true;
                array_push($messages, trans('front.product.add_cart_complete'));
            } else {
                // エラーが発生している場合
                $done = false;
                $messages = $errorMessages;
            }

            return $this->json(['done' => $done, 'messages' => $messages]);
        } else {
            // ajax以外でのリクエストの場合はカート画面へリダイレクト
            foreach ($errorMessages as $errorMessage) {
                $this->addRequestError($errorMessage);
            }
            if ($request->get('_locale') != Constants::LOCALE_JAPAN && $request->get('_locale') != null) {
                return $this->redirectToRoute('cart_locale');
            }

            return $this->redirectToRoute('cart');
        }
    }

    /**
     * カートに追加.
     *
     * @Route("/{_locale}/products/add_cart/{id}", name="product_add_cart_locale", methods={"POST"}, requirements={"id" = "\d+"})
     */
    public function addCartLocale(Request $request, Product $Product)
    {
        return $this->addCart($request, $Product);
    }

    /**
     * ページタイトルの設定
     *
     * @param  array|null $searchData
     *
     * @return string
     */
    protected function getPageTitle($searchData)
    {
        if (isset($searchData['name']) && !empty($searchData['name'])) {
            return trans('front.product.search_result');
        } elseif (isset($searchData['category_id']) && $searchData['category_id']) {
            return $searchData['category_id']->getName();
        } else {
            return trans('front.product.all_products');
        }
    }

    /**
     * 閲覧可能な商品かどうかを判定
     *
     * @param Product $Product
     *
     * @return bool 閲覧可能な場合はtrue
     */
    protected function checkVisibility(Product $Product)
    {
        $is_admin = $this->session->has('_security_admin');

        // 管理ユーザの場合はステータスやオプションにかかわらず閲覧可能.
        if (!$is_admin) {
            if ($Product->getStatus()->getId() !== ProductStatus::DISPLAY_SHOW) {
                return false;
            }
        }

        return true;
    }

    // check if product language is same with url locale
    public function validateLocale(Request $request, Product $product)
    {
        // get locale from user request
        $localeUrl = $request->getLocale();

        try {
            // find record in database
            $result = $this->entityManager->getRepository(PlgExpandProductColumnsValue::class)->findOneBy([
                'productId' => $product->getId(),
                'columnId' => Constants::LOCALE_RECORD_ID,
            ]);

            $localeSetting = empty($result) ? Constants::LOCALE_JAPAN : $result->getValue();
        } catch (\Exception $ex) {
            log_info('Error when get value working with plg_expand_product_columns_value table');
            $localeSetting = Constants::LOCALE_JAPAN;
        }
        // if product locale not same with url access
        $isPass = $localeUrl == $localeSetting;

        return [
            'isPass' => $isPass,
            'locale' => $localeSetting,
        ];
    }
}

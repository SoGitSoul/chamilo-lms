<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Menu;

use Chamilo\PageBundle\Entity\Page;
use Chamilo\PageBundle\Entity\Site;
use Chamilo\UserBundle\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class NavBuilder
 * @package Chamilo\CoreBundle\Menu
 */
class NavBuilder extends ContainerAware
{
    /**
     * @param array  $itemOptions The options given to the created menuItem
     * @param string $currentUri  The current URI
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createCategoryMenu(array $itemOptions = array(), $currentUri = null)
    {
        $factory = $this->container->get('knp_menu.factory');
        $menu = $factory->createItem('categories', $itemOptions);

        $this->buildCategoryMenu($menu, $itemOptions, $currentUri);

        return $menu;
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu        The item to fill with $routes
     * @param array                   $options     The item options
     * @param string                  $currentUri  The current URI
     */
    public function buildCategoryMenu(ItemInterface $menu, array $options = array(), $currentUri = null)
    {
        //$categories = $this->categoryManager->getCategoryTree();

        //$this->fillMenu($menu, $categories, $options, $currentUri);
        $menu->addChild('home', array('route' => 'home'));
    }

    /**
     * Top menu left
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function leftMenu(FactoryInterface $factory, array $options)
    {
        $checker = $this->container->get('security.authorization_checker');
        $translator = $this->container->get('translator');

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild(
            $translator->trans('Home'),
            array('route' => 'home')
        );

        if ($checker->isGranted('IS_AUTHENTICATED_FULLY')) {

            $menu->addChild(
                $translator->trans('MyCourses'),
                array('route' => 'userportal')
            );

            $menu->addChild(
                $translator->trans('Calendar'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'calendar/agenda_js.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('Reporting'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'mySpace/index.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('Social'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/home.php',
                    ),
                )
            );

            if ($checker->isGranted('ROLE_ADMIN')) {

                $menu->addChild(
                    $translator->trans('Dashboard'),
                    array(
                        'route' => 'main',
                        'routeParameters' => array(
                            'name' => 'dashboard/index.php',
                        ),
                    )
                );

                $menu->addChild(
                    $translator->trans('Administration'),
                    array(
                        'route' => 'administration',
                    )
                );
            }
        }

        $categories = $this->container->get('faq.entity.category_repository')->retrieveActive();
        if ($categories) {
            $faq = $menu->addChild(
                'FAQ',
                [
                    'route' => 'faq_index',
                ]
            );
            /** @var Category $category */
            foreach ($categories as $category) {
                 $faq->addChild(
                    $category->getHeadline(),
                    array(
                        'route' => 'faq',
                        'routeParameters' => array(
                            'categorySlug' => $category->getSlug(),
                            'questionSlug' => '',
                        ),
                    )
                )->setAttribute('divider_append', true);
            }
        }

        // Getting site information

        $site = $this->container->get('sonata.page.site.selector');
        $host = $site->getRequestContext()->getHost();
        $siteManager = $this->container->get('sonata.page.manager.site');
        /** @var Site $site */
        $site = $siteManager->findOneBy(array(
            'host'    => array($host, 'localhost'),
            'enabled' => true,
        ));

        if ($site) {
            $pageManager = $this->container->get('sonata.page.manager.page');

            // Parents only of homepage
            $criteria = ['site' => $site, 'enabled' => true, 'parent' => 1];
            $pages = $pageManager->findBy($criteria);

            //$pages = $pageManager->loadPages($site);
            /** @var Page $page */
            foreach ($pages as $page) {
                /*if ($page->getRouteName() !== 'page_slug') {
                    continue;
                }*/

                // Avoid home
                if ($page->getUrl() === '/') {
                    continue;
                }

                if (!$page->isCms()) {
                    continue;
                }

                $subMenu = $menu->addChild(
                    $page->getName(),
                    [
                        'route' => $page->getRouteName(),
                        'routeParameters' => [
                            'path' => $page->getUrl(),
                        ],
                    ]
                );

                /** @var Page $child */
                foreach ($page->getChildren() as $child) {
                    $subMenu->addChild(
                        $child->getName(),
                        array(
                            'route' => $page->getRouteName(),
                            'routeParameters' => array(
                                'path' => $child->getUrl(),
                            ),
                        )
                    )->setAttribute('divider_append', true);
                }
            }
        }


        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function profileMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $security = $this->container->get('security.context');
        $translator = $this->container->get('translator');

        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked');

            $menu->addChild(
                $translator->trans('Inbox'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'messages/inbox.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('Compose'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'messages/new_message.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('PendingInvitations'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/invitations.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('MyFiles'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/myfiles.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('EditProfile'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'messages/inbox.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('Inbox'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'messages/inbox.php',
                    ),
                )
            );
        }

        return $menu;
    }

    /**
     * @todo add validations
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function socialMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $security = $this->container->get('security.context');
        $translator = $this->container->get('translator');

        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked');

            $menu->addChild(
                $translator->trans('Home'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/home.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('Messages'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'messages/inbox.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('Invitations'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/invitations.php',
                    ),
                )
            );


            $menu->addChild(
                $translator->trans('ViewMySharedProfile'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/profile.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('Friends'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/friends.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('SocialGroups'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/groups.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('Search'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/search.php',
                    ),
                )
            );

            $menu->addChild(
                $translator->trans('MyFiles'),
                array(
                    'route' => 'main',
                    'routeParameters' => array(
                        'name' => 'social/myfiles.php',
                    ),
                )
            );
        }

        return $menu;
    }
}

<?php
declare(strict_types=1);

namespace App\Menu;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;
    /**
     * @var MenuRepository
     */
    private $repository;

    public function __construct(FactoryInterface $factory, MenuRepository $menuRepository)
    {
        $this->factory = $factory;
        $this->repository = $menuRepository;
    }

    public function mainMenu(): ItemInterface
    {
        $items = $this->countingSort(
            $this->repository->findAll()
        );

        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto'); // set <ul> attribute

        /* @var $menuElement Menu */
        foreach ($items as $menuElement)
        {
            if(!$menuElement instanceof Menu) {
                continue;
            }

            $newMenuLabel = $this->factory->createItem($menuElement->getCategory(), [
                'route' => strtolower((new \ReflectionClass($menuElement))->getShortName()),
                'routeParameters' => [
                    'menu' => $this->generateRouteParameters($menuElement)
                ]
            ]);

            if($menuElement->getParent() !== null) {

                $parentMenuLabel = $this->getMenuLabel($menu, $menuElement);

                // add dropdown css property to parent label
                $parentMenuLabel->getParent() !== null && $parentMenuLabel->getParent()->getName() !== 'root' ?
                    $parentMenuLabel->setAttribute('class', 'dropdown-item dropdown-submenu') :
                    $parentMenuLabel->setAttribute('class', 'nav-item dropdown');   // </li>

                $parentMenuLabel->setLinkAttribute('class', 'nav-link dropdown-toggle');    // <a>
                $parentMenuLabel->setLinkAttribute('id', "{$menuElement->getParent()->getSlug()}");
                $parentMenuLabel->setLinkAttribute('role', 'button');
                $parentMenuLabel->setLinkAttribute('data-toggle', 'dropdown');
                $parentMenuLabel->setLinkAttribute('aria-haspopup', 'true');
                $parentMenuLabel->setLinkAttribute('aria-expanded', 'false');

                $parentMenuLabel->setChildrenAttribute('class', 'dropdown-menu');   // eg <ul>

                // add sub menu style to new label
                $parentMenuLabel->setChildrenAttribute('aria-labelledby', "{$menuElement->getSlug()}");
                $newMenuLabel->setAttribute('class', 'dropdown-item');  // <li>
                $newMenuLabel->setLinkAttribute('class', 'nav-link');   // <a>

            } else {
                $parentMenuLabel = $menu;

                $newMenuLabel->setAttribute('class', 'nav-item');
                $newMenuLabel->setLinkAttribute('class', 'nav-link');
            }

            $parentMenuLabel->addChild($newMenuLabel);
        }

        return $menu;
    }

    private function countingSort(array $menuUnorderedList): array
    {
        $supportArray = [];
        $menuOrderedArray = [];
        $maxDeep  = 1;

        foreach ($menuUnorderedList as $menuElement) {
            if($maxDeep < $menuElement->getHierarchy()) {
                $maxDeep = $menuElement->getHierarchy();
            }
        }

        for($i = 0, $iMax = count($menuUnorderedList) + 1; $i < $iMax; $i++) {
            $menuOrderedArray[$i] = 0;
        }

        for($i = 0; $i < $maxDeep+1; $i++) {
            $supportArray[$i] = 0;
        }

        foreach ($menuUnorderedList as $menuElement) {
            ++$supportArray[$menuElement->getHierarchy()];
        }

        for($i = 1; $i < $maxDeep+1; $i++) {
            $supportArray[$i] += $supportArray[$i - 1];
        }

        for($index = count($menuUnorderedList)-1; $index>=0; $index--) {
            $menuOrderedArray[$supportArray[$menuUnorderedList[$index]->getHierarchy()]] = $menuUnorderedList[$index];
            --$supportArray[$menuUnorderedList[$index]->getHierarchy()];
        }

        return $menuOrderedArray;
    }

    private function getMenuLabel(ItemInterface $core, ?Menu $currentMenuLabel): ItemInterface
    {
        if($currentMenuLabel === null)
            return $core;

        $label = $this->getMenuLabel($core, $currentMenuLabel->getParent());
        return $label[$currentMenuLabel->getCategory()] ?? $label;
    }

    // TODO: change route: from a+b+c to a/b/c
    private function generateRouteParameters(Menu $currentMenuLabel): string
    {
        if($currentMenuLabel->getParent() === null)
            return $currentMenuLabel->getSlug();

        return
            str_replace(' ', '+',
                $this->generateRouteParameters($currentMenuLabel->getParent()) . ' ' . $currentMenuLabel->getSlug()
            );
    }
}



/*
 * to fix template rendering problem:
 * https://github.com/KnpLabs/KnpMenu/issues/283
 * */
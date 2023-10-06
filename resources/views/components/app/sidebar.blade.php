@php
    $menus = [
        [ "url" => "dashboard.index", "label" => "Dashboard", "icon" => "", "roles" => ["SUPERADMIN", "SALES", "PURCHASE", "MANAGER"] ],
        [ "url" => "inventory.index", "label" => "Inventory", "icon" => "", "roles" => ["SUPERADMIN"] ],
        [ "url" => "sales.index", "label" => "Sales", "icon" => "", "roles" => ["SUPERADMIN", "SALES", "MANAGER"] ],
        [ "url" => "purchase.index", "label" => "Purchase", "icon" => "", "roles" => ["SUPERADMIN", "PURCHASE", "MANAGER"] ],
    ];
@endphp

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Test Fanatech</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">TF</a>
        </div>
        <ul class="sidebar-menu">
            @foreach ($menus as $menu)
                @role($menu['roles'])
                    <li class="{{ Route::currentRouteName() == $menu['url'] ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route($menu['url']) }}"><i class="far fa-square"></i> <span>{{ $menu['label'] }}</span></a>
                    </li>
                @endrole
            @endforeach
        </ul>
    </aside>
</div>


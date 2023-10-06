<div class="section-header">
    <div class="section-header-breadcrumb">
        @foreach ($menus as $menu)
            <div class="breadcrumb-item active"><a href="{{ $menu['name'] }}">{{ $menu['label'] }}</a></div>
        @endforeach
        <div class="breadcrumb-item">{{ $title }}</div>
    </div>
</div>
